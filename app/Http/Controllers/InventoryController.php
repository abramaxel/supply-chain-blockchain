<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Item;

class InventoryController extends Controller
{
    // InventoryController.php

   public function index(Request $request)
    {
        $search = request('search');
        $perPage = request('per_page', 10);

        $items = DB::table('items')
            ->leftJoin('batches', 'items.id', '=', 'batches.item_id')
            ->select(
                'items.id',
                'items.item_code',
                'items.name',
                'items.type',
                'items.unit',
                DB::raw('COALESCE(SUM(batches.quantity), 0) as total_stock')
            )
            ->groupBy('items.id', 'items.item_code', 'items.name', 'items.type', 'items.unit');

        if ($search) {
            $items = $items->where(function ($query) use ($search) {
                $query->where('items.item_code', 'like', "%$search%")
                    ->orWhere('items.name', 'like', "%$search%")
                    ->orWhere('items.type', 'like', "%$search%")
                    ->orWhere('items.unit', 'like', "%$search%");
            });
        }

        $items = $items->paginate($perPage)->withQueryString();

        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|unique:items,item_code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:raw_material,finished_good',
            'unit' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan data item
            $item = Item::create([
                'id' => (string) Str::uuid(),
                'item_code' => $request->item_code,
                'name' => $request->name,
                'type' => $request->type,
                'unit' => $request->unit,
            ]);

            // 2. Cari previous hash untuk semua entity_type = 'inventory' (BUKAN per item_code!)
            $previousBlock = DB::table('blockchain_logs')
                ->where('entity_type', 'inventory')
                ->orderByDesc('id')->first();
            $previousHash = $previousBlock ? $previousBlock->block_hash : null;

            $now = now()->format('Y-m-d H:i:s');
            $data = $item->toArray();
            ksort($data); // urut key sebelum encode
            $hashString = json_encode($data) . $previousHash . $now;
            $blockHash = hash('sha256', $hashString);

            if (app()->environment(['local', 'testing'])) {
                Log::info('HASH_INSERT', ['string' => $hashString]);
            }

            DB::table('blockchain_logs')->insert([
                'block_hash'    => $blockHash,
                'previous_hash' => $previousHash,
                'entity_type'   => 'inventory',
                'entity_id'     => $item->item_code,
                'action'        => 'CREATE',
                'data'          => json_encode($data),
                'user_id'       => Auth::id(),
                'created_at'    => $now,
            ]);

            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventory.index')->with('error', 'Gagal menambahkan barang: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Ambil data item berdasarkan ID (bisa UUID)
        $item = Item::findOrFail($id);

        // Jika tipe barang ada enum, bisa dibuat array untuk dropdown (optional)
        $types = [
            'raw_material'   => 'Raw Material',
            'finished_good'  => 'Finished Good',
        ];

        // Tampilkan view edit dengan data item dan tipe (jika perlu)
        return view('inventory.edit', compact('item', 'types'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'item_code' => 'required|unique:items,item_code,' . $item->id,
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:raw_material,finished_good',
            'unit'      => 'nullable|string|max:255',
        ]);

        $item->update([
            'item_code' => $request->item_code,
            'name'      => $request->name,
            'type'      => $request->type,
            'unit'      => $request->unit,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function mutation($id)
    {
        $item = DB::table('items')->where('id', $id)->first();

        if (!$item) {
            return redirect()->route('inventory.index')->with('error', 'Barang tidak ditemukan');
        }

        $batches = DB::table('batches')
            ->where('item_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Di sini kamu bisa tambahkan logika mutasi lain (penjualan, pengeluaran, dll)
        // Untuk saat ini kita hanya tampilkan batch sebagai mutasi masuk

        return view('inventory.mutation', compact('item', 'batches'));
    }

}
