<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;

class PerawatDashboardController extends Controller
{
    public function index()
    {
        return view('perawat.dashboard');
    }

    public function antrian()
    {
        $antrians = \App\Models\Antrian::with(['pasien', 'poli'])->get();
        return view('perawat.antrian', compact('antrians'));
    }
}
