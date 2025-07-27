<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputSuratMasukController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required',
            'pengirim' => 'required',
            'penerima' => 'required',
        ]);
    }
}
