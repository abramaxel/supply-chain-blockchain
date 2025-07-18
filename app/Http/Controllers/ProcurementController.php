<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Batch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Item; // Untuk dropdown barang

class ProcurementController extends Controller
{
    // Tampilkan daftar semua purchase order beserta relasi item-nya
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('items')->orderByDesc('order_date');

        // Filter pencarian jika ada request 'search'
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Jika ingin pagination, lebih baik gunakan paginate, bukan get
        $purchaseOrders = $query->paginate(10);


        return view('procurement.index', compact('purchaseOrders'));
    }

    // Tampilkan form create PO
    public function create()
    {
        // Ambil semua barang (item) untuk pilihan di form
        $items = Item::orderBy('name')->get();
        return view('procurement.create', compact('items'));
    }

    // Proses simpan data PO baru beserta itemnya
    public function store(Request $request)
    {
        $request->validate([
            'po_number' => 'required|unique:purchase_orders,po_number',
            'order_date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'status' => 'required|in:open,approved,closed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.location' => 'required|string|max:255',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $request->po_number,
                'order_date' => $request->order_date,
                'supplier' => $request->supplier,
                'status' => $request->status,
            ]);

            foreach ($request->items as $poItem) {
                // --- Pastikan batch sudah ada di tabel batches ---
                if (!empty($poItem['batch_no'])) {
                    Batch::firstOrCreate(
                        ['batch_no' => $poItem['batch_no']],
                        [
                            'item_id'     => $poItem['item_id'],
                            'quantity'    => $poItem['quantity'],
                            'location'    => $poItem['location'],
                            'expiry_date' => $poItem['expiry_date'] ?? null,
                            'batch_hash'  => hash('sha256', $poItem['batch_no'] . $poItem['item_id'] . now())
                        ]
                    );
                }

                $poItemModel = PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id'           => $poItem['item_id'],
                    'batch_no'          => $poItem['batch_no'] ?? null,
                    'quantity'          => $poItem['quantity'],
                    'unit_price'        => $poItem['unit_price'],
                    'total_price'       => $poItem['unit_price'] * $poItem['quantity'],
                    'location'          => $poItem['location'],
                    'expiry_date'       => $poItem['expiry_date'] ?? null,
                ]);
                    // ==== Simpan ke blockchain_logs ====
            $entityType = 'purchase_order_item';
            $entityId = $poItemModel->id;

            // Cari previous hash (chain per item PO)
            $previousBlock = DB::table('blockchain_logs')
                ->where('entity_type', $entityType)
                ->orderByDesc('id')->first();
            $previousHash = $previousBlock ? $previousBlock->block_hash : null;

            // Buat snapshot data untuk blockchain log
            $data = [
                'purchase_order' => $purchaseOrder->only(['id', 'po_number']),
                'item' => $poItemModel->toArray(),
                'batch_no' => $poItem['batch_no'] ?? null,
                'location' => $poItem['location'] ?? null,
                'expiry_date' => $poItem['expiry_date'] ?? null,
            ];

            $now = now()->format('Y-m-d H:i:s');
            $data = $poItemModel->toArray();
            ksort($data); // atau $item->toArray() di inventory
            $hashString = json_encode($data) . $previousHash . $now;
            //Log::info('HASH_INSERT', ['string' => $hashString]);
            $blockHash = hash('sha256', $hashString);
            if (app()->environment(['local', 'testing'])) {
                Log::info('HASH_INSERT', ['string' => $hashString]);
            }
            
            // Simpan block ke blockchain_logs
            DB::table('blockchain_logs')->insert([
                'block_hash'    => $blockHash,
                'previous_hash' => $previousHash,
                'entity_type'   => $entityType,
                'entity_id'     => $entityId,
                'action'        => 'CREATE',
                'data'          => json_encode($data),
                'user_id'       => Auth::id(),
                'created_at'    => $now,
            ]);
        } 
            DB::commit();
            return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat Purchase Order: ' . $e->getMessage());
        }
    }

    public function trace($id)
    {
        // Ambil data PO + item
        $purchaseOrder = PurchaseOrder::with(['items', 'items.item', 'items.batch'])->findOrFail($id);

        // Ambil blockchain_logs untuk semua item di PO ini
        $itemIds = $purchaseOrder->items->pluck('id')->toArray();

        // Ambil semua log blockchain untuk item-item PO ini, urut dari awal ke akhir
        $logs = DB::table('blockchain_logs')
            ->where('entity_type', 'purchase_order_item')
            ->whereIn('entity_id', $itemIds)
            ->orderBy('id')
            ->get();

        // Bisa juga groupBy entity_id jika ingin trace per item
        $logsByItem = $logs->groupBy('entity_id');

        // Ambil log blockchain gabungan (untuk seluruh item PO)
        $itemIds = $purchaseOrder->items->pluck('id')->toArray();
        $traceLogs = DB::table('blockchain_logs')
            ->where('entity_type', 'purchase_order_item')
            ->whereIn('entity_id', $itemIds)
            ->orderBy('id')
            ->get()
            ->map(function ($log) {
                $log->hash = $log->block_hash ?? '';
                return $log;
            });


        return view('procurement.trace', [
            'purchaseOrder' => $purchaseOrder,
            'logsByItem'    => $logsByItem,
            'traceLogs'     => $traceLogs
        ]);
    }

    public function show($id)
    {
        // Ambil data PO lengkap beserta item dan relasi ke barang
        $purchaseOrder = PurchaseOrder::with(['items', 'items.item'])->findOrFail($id);

        // Kirim ke view
        return view('procurement.show', [
            'order' => $purchaseOrder,
        ]);
    }
    public function previewPdf($orderId)
    {
        $order = PurchaseOrder::with('items.item')->findOrFail($orderId);
        $pdf = Pdf::loadView('procurement.pdf', compact('order'));
        // Generate PDF as string, NOT download
        $output = $pdf->output();
        // Response PDF inline (show in browser)
        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="PO_'.$order->po_number.'.pdf"');
    }

    public function downloadPdf($orderId)
    {
        $order = PurchaseOrder::with('items.item')->findOrFail($orderId);
        $pdf = Pdf::loadView('procurement.pdf', compact('order'));
        $filename = 'PO_'.$order->po_number.'.pdf';
        return $pdf->download($filename);
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with(['items', 'items.item'])->findOrFail($id);
        $items = Item::orderBy('name')->get(); // Untuk dropdown pilihan barang

        return view('procurement.edit', [
            'order' => $purchaseOrder,
            'items' => $items
        ]);
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);

        $request->validate([
            'po_number' => 'required|unique:purchase_orders,po_number,' . $purchaseOrder->id,
            'order_date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'status' => 'required|in:open,approved,closed,cancelled',
            // Optional: validasi item jika ingin update item juga
        ]);

        // Update data utama PO
        $purchaseOrder->update([
            'po_number' => $request->po_number,
            'order_date' => $request->order_date,
            'supplier' => $request->supplier,
            'status' => $request->status,
        ]);

        // Note: Jika ingin update item PO juga, perlu logic tambahan (hapus, tambah, edit item satu per satu)

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil diupdate!');
    }

        public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        // Hapus semua item terkait (jika relasi onDelete cascade, bisa lewati baris ini)
        // $purchaseOrder->items()->delete();

        // Hapus PO
        $purchaseOrder->delete();

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dihapus!');
    }



}
