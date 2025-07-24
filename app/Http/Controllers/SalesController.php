<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    // SalesOrderController@index
    public function index(Request $request)
    {
        $query = SalesOrder::with('items')->orderByDesc('order_date');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'like', "%$search%")
                ->orWhere('customer', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }

        $orders = $query->paginate(10);

        // Hitung total_price untuk setiap order
        foreach ($orders as $order) {
            $order->total_price = $order->items->sum(function ($item) {
                return $item->unit_selling_price * $item->quantity;
            });
        }

        return view('sales.index', compact('orders'));
    }


    // Tampilkan form create sales order
    public function create()
    {
        $items = Item::where('type', 'finished_good')->orderBy('name')->get();

        // Hitung batch yang masih punya stok
        $batches = collect();
        foreach ($items as $item) {
            $itemBatches = Batch::where('item_id', $item->id)->get();
            $filteredBatches = [];

            foreach ($itemBatches as $batch) {
                $sold = SalesOrderItem::where('item_id', $batch->item_id)
                    ->where('batch_no', $batch->batch_no)
                    ->sum('quantity');

                $available = $batch->quantity - $sold;

                // Hanya tambahkan jika stok masih ada
                if ($available > 0) {
                    $filteredBatches[] = [
                        'item_id' => $batch->item_id,
                        'batch_no' => $batch->batch_no,
                        'available' => $available,
                        'location' => $batch->location,
                    ];
                }
            }

            if (!empty($filteredBatches)) {
                $batches[$item->id] = $filteredBatches;
            }
        }

        return view('sales.create', compact('items', 'batches'));
    }

    // Simpan sales order baru beserta itemnya
    public function store(Request $request)
    {
        $request->validate([
            'so_number' => 'required|unique:sales_orders,so_number',
            'order_date' => 'required|date',
            'customer' => 'required|string|max:255',
            'status' => 'required|in:open,approved,closed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Cek stok per batch
            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'];
                $batchNo = $itemData['batch_no'];
                $requestedQty = $itemData['quantity'];

                // Ambil stok dari batch tertentu
                $batch = Batch::where('item_id', $itemId)
                    ->where('batch_no', $batchNo)
                    ->first();

                if (!$batch) {
                    throw new \Exception("Batch $batchNo tidak ditemukan untuk barang ini.");
                }

                // Hitung total penjualan dari batch ini
                $soldFromThisBatch = SalesOrderItem::where('item_id', $itemId)
                    ->where('batch_no', $batchNo)
                    ->sum('quantity');

                $availableStock = $batch->quantity - $soldFromThisBatch;

                if ($requestedQty > $availableStock) {
                    $itemName = $batch->item->name ?? 'Barang';
                    throw new \Exception("Stok batch $batchNo untuk $itemName tidak mencukupi. Diminta: $requestedQty, Tersedia: $availableStock");
                }
            }

            // Lanjutkan simpan data
            $salesOrder = SalesOrder::create([
                'so_number' => $request->input('so_number'),
                'order_date' => $request->input('order_date'),
                'customer' => $request->input('customer'),
                'status' => $request->input('status'),
            ]);

            foreach ($request->items as $itemData) {
                $salesItem = SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $itemData['item_id'],
                    'batch_no' => $itemData['batch_no'],
                    'quantity' => $itemData['quantity'],
                    'unit_selling_price' => $itemData['unit_price'],
                    'total_price' => $itemData['unit_price'] * $itemData['quantity'],
                ]);

                // Simpan ke blockchain_logs
                $entityType = 'sales_order_item';
                $entityId = $salesItem->id;

                $previousBlock = DB::table('blockchain_logs')
                    ->where('entity_type', $entityType)
                    ->orderByDesc('id')->first();
                $previousHash = $previousBlock ? $previousBlock->block_hash : null;

                $data = [
                    'sales_order' => $salesOrder->only(['id', 'so_number']),
                    'item' => $salesItem->toArray(),
                ];

                $now = now()->format('Y-m-d H:i:s');
                $hashString = json_encode($salesItem->toArray()) . $previousHash . $now;
                $blockHash = hash('sha256', $hashString);

                DB::table('blockchain_logs')->insert([
                    'block_hash' => $blockHash,
                    'previous_hash' => $previousHash,
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'action' => 'CREATE',
                    'data' => json_encode($data),
                    'user_id' => Auth::id(),
                    'created_at' => $now,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Sales Order berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified sales order.
     */
    public function edit($id)
    {
        $order = SalesOrder::with('items')->findOrFail($id);
        $items = Item::where('type', 'finished_good')->orderBy('name')->get();
        $batches = \App\Models\Batch::with('item')->get();

        return view('sales.edit', compact('order', 'items', 'batches'));
    }

    /**
     * Display the specified sales order.
     */
    public function show($id)
    {
        $order = SalesOrder::with('items')->findOrFail($id);
        return view('sales.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $salesOrder = SalesOrder::findOrFail($id);

            // Validasi input
            $request->validate([
                'so_number' => 'required|unique:sales_orders,so_number,' . $id,
                'order_date' => 'required|date',
                'customer' => 'required|string|max:255',
                'status' => 'required|in:open,approved,closed,cancelled',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.batch_no' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            // Update sales order
            $salesOrder->update([
                'so_number' => $request->input('so_number'),
                'order_date' => $request->input('order_date'),
                'customer' => $request->input('customer'),
                'status' => $request->input('status'),
            ]);

            // Hapus item lama
            SalesOrderItem::where('sales_order_id', $id)->delete();

            // Simpan item baru
            $items = $request->input('items', []);

            if (!is_array($items)) {
                $items = [];
            }

            foreach ($items as $itemData) {
                /** @var \App\Models\SalesOrderItem $salesItem */
                $salesItem = SalesOrderItem::create([
                    'sales_order_id' => $id,
                    'item_id' => $itemData['item_id'],
                    'batch_no' => $itemData['batch_no'],
                    'quantity' => $itemData['quantity'],
                    'unit_selling_price' => $itemData['unit_price'],
                    'total_price' => $itemData['unit_price'] * $itemData['quantity'],
                ]);

                // Simpan ke blockchain_logs
                $entityType = 'sales_order_item';
                $entityId = $salesItem->id;

                $previousBlock = DB::table('blockchain_logs')
                    ->where('entity_type', $entityType)
                    ->orderByDesc('id')
                    ->first();

                $previousHash = $previousBlock ? $previousBlock->block_hash : null;

                $data = [
                    'sales_order' => $salesOrder->only(['id', 'so_number']),
                    'item' => $salesItem->toArray(),
                ];

                $now = now()->format('Y-m-d H:i:s');
                $hashString = json_encode($salesItem->toArray()) . $previousHash . $now;
                $blockHash = hash('sha256', $hashString);

                DB::table('blockchain_logs')->insert([
                    'block_hash' => $blockHash,
                    'previous_hash' => $previousHash,
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'action' => 'CREATE',
                    'data' => json_encode($data),
                    'user_id' => Auth::id(),
                    'created_at' => $now,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Sales Order berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Sales Order: ' . $e->getMessage());
        }
    }
}
