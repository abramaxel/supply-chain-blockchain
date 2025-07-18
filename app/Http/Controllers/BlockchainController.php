<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BlockchainController extends Controller
{
    // Menampilkan semua block sebagai card, dengan search & filter & pagination
    public function index(Request $request)
    {
        $query = DB::table('blockchain_logs');

        // Fitur filter/search:
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('search') && $request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('block_hash', 'like', "%$s%")
                  ->orWhere('previous_hash', 'like', "%$s%")
                  ->orWhere('entity_id', 'like', "%$s%");
            });
        }

        $blocks = $query->orderByDesc('id')->paginate(20); // pagination 20/block

        // Untuk filter dropdown entity/user (opsional)
        $entityTypes = DB::table('blockchain_logs')->select('entity_type')->distinct()->pluck('entity_type');
        $users = DB::table('blockchain_logs')->select('user_id')->distinct()->pluck('user_id');

        return view('blockchain.index', compact('blocks', 'entityTypes', 'users'));
    }

    // Menampilkan detail satu block (dari tombol detail pada card)
    public function show($id)
    {
        $block = DB::table('blockchain_logs')->find($id);
        if (!$block) abort(404);

        // Untuk tampilan detail, decode data jika ingin
        $block->data_pretty = json_encode(json_decode($block->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return view('blockchain.detail', compact('block'));
    }
    
    public function verify($id)
    {
        $block = DB::table('blockchain_logs')->find($id);
        if (!$block) abort(404);

        $hashString = $block->data . $block->previous_hash . $block->created_at;
        $rehashed = hash('sha256', $hashString);

        // Hanya log jika hash tidak valid
        if ($block->block_hash !== $rehashed) {
            Log::warning('HASH_VERIFY_FAILED', [
                'block_id' => $block->id,
                'hashString' => $hashString,
                'block_hash' => $block->block_hash,
                'rehashed'   => $rehashed,
            ]);
        }

        return response()->json([
            'isValid'      => $block->block_hash === $rehashed,
            'originalHash' => $block->block_hash,
            'rehashed'     => $rehashed,
            'hashString'   => $hashString,
        ]);
    }

}
