<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Item;
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
        return view('sales.index', compact('orders'));
    }


    // Tampilkan form create sales order
    public function create()
    {
        // Ambil semua item (finished_good saja misal)
        $items = Item::where('type', 'finished_good')->orderBy('name')->get();

        // Ambil semua batch (relasi ke item)
        $batches = \App\Models\Batch::with('item')->get();

        // Kirim ke view
        return view('sales.create', compact('items', 'batches'));
    }

    // Simpan sales order baru beserta itemnya
    public function store(Request $request)
    {
        $request->validate([
            'so_number' => 'required|unique:sales_orders,so_number',
            'order_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'status' => 'required|in:open,approved,closed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $salesOrder = SalesOrder::create([
                'so_number' => $request->so_number,
                'order_date' => $request->order_date,
                'customer_name' => $request->customer_name,
                'status' => $request->status,
            ]);

            foreach ($request->items as $itemData) {
                $salesItem = SalesOrderItem::create([
                    'sales_order_id'      => $salesOrder->id,
                    'item_id'             => $itemData['item_id'],
                    'batch_no'            => $itemData['batch_no'],
                    'quantity'            => $itemData['quantity'],
                    'unit_selling_price'  => $itemData['unit_price'],
                    'total_price'         => $itemData['unit_price'] * $itemData['quantity'],
                ]);

                // ==== Simpan ke blockchain_logs ====
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
            return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat Sales Order: ' . $e->getMessage());
        }
    }


}
