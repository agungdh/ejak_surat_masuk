<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\PangkatGolongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.dashboard', compact([

        ]));
    }

    public function profil(Request $request)
    {
        $pegawai = Auth::user()->pegawai;

        $bidangs = Bidang::all();
        $pangkats = PangkatGolongan::select(['id', 'pangkat', 'golongan', 'ruang'])->get();

        return view('pages.profil', compact([
            'pegawai',
            'bidangs',
            'pangkats',
        ]));
    }

    public function profilData(Request $request)
    {
        $pegawai = Auth::user()->pegawai;

        return $pegawai;
    }

    public function profilUpdate(Request $request)
    {
        $pegawai = Auth::user()->pegawai;

        $user = $pegawai->user;

        $request->validate([
            'nip' => [
                'required',
                'integer',
                Rule::unique('pegawais', 'nip')->ignore($pegawai->id),
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'nama' => 'required',
            'password' => 'confirmed',
            'bidang_id' => 'required|exists:bidangs,id',
            'pangkat_golongan_id' => 'nullable|exists:pangkat_golongans,id',
        ]);

        DB::transaction(function () use ($request, $pegawai, $user) {
            $user->username = $request->nip;
            if ($request->password) {
                $user->password = $request->password;
            }
            $user->save();

            $pegawai->user_id = $user->id;
            $pegawai->nip = $request->nip;
            $pegawai->nama = $request->nama;
            $pegawai->pangkat_golongan_id = $request->pangkat_golongan_id;
            $pegawai->jabatan = $request->jabatan;
            $pegawai->bidang_id = $request->bidang_id;
            $pegawai->save();
        });

        $request->session()->flash('success', 'Profil berhasil disimpan.');
    }
}
