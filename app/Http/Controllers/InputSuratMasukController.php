<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputSuratMasukController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws \Throwable
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required',
            'pengirim' => 'required',
            'penerima' => 'required',
            'berkas' => 'required|file|mimes:pdf',
        ]);

        DB::transaction(function () use ($request) {
            $suratMasuk = new SuratMasuk;
            $suratMasuk->nomor_surat = $request->nomor_surat;
            $suratMasuk->tanggal_surat = $request->tanggal_surat;
            $suratMasuk->perihal = $request->perihal;
            $suratMasuk->pengirim = $request->pengirim;
            $suratMasuk->penerima = $request->penerima;
            $suratMasuk->save();

            $request->file('berkas')->storeAs('surat_masuk', $suratMasuk->id);
        });

    }
}
