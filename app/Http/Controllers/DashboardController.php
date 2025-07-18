<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Bisa load data ringkasan di sini, contoh sederhana:
        // $totalBlocks = \DB::table('blocks')->count();
        // $totalItems = \DB::table('items')->count();
        // return view('dashboard.index', compact('totalBlocks', 'totalItems'));
        return view('dashboard.index');
    }
}
