<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\PangkatGolongan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PegawaiController extends Controller
{
    public function datatable(Request $request)
    {
        $datas = Pegawai::from((new Pegawai)->getTable().' as p');

        $datas = $datas->join((new Bidang)->getTable().' as b', 'p.bidang_id', '=', 'b.id');
        $datas = $datas->leftJoin((new PangkatGolongan)->getTable().' as pg', 'p.pangkat_golongan_id', '=', 'pg.id');

        $datas->select([
            'p.*',
            'b.bidang',
            'pg.pangkat',
            'pg.golongan',
            'pg.ruang',
        ]);

        if ($request->tipe) {
            $datas = $datas->where('tipe', $request->tipe);
        }

        if ($request->status) {
            $datas = $datas->where('status', $request->status);
        }

        if ($request->bidang_id) {
            $datas = $datas->where('bidang_id', $request->bidang_id);
        }

        if ($request->pangkat_golongan_id) {
            $datas = $datas->where('pangkat_golongan_id', $request->pangkat_golongan_id);
        }

        return DataTables::of($datas)
            ->addColumn('action', function ($row) {
                return view('pages.pegawai.action', ['row' => $row])->render();
            })
            ->editColumn('tipe', function ($row) {
                return ucwords($row->tipe);
            })
            ->editColumn('status', function ($row) {
                return ucwords($row->status);
            })
            ->editColumn('pangkat_golongan', function ($row) {
                return $row->pangkat ? "$row->pangkat ($row->golongan/$row->ruang)" : '-';
            })
            ->make();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bidangs = Bidang::select(['id', 'bidang'])->get();
        $pangkats = PangkatGolongan::select(['id', 'pangkat', 'golongan', 'ruang'])->get();

        return view('pages.pegawai.index', compact([
            'bidangs',
            'pangkats',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bidangs = Bidang::all();
        $pangkats = PangkatGolongan::select(['id', 'pangkat', 'golongan', 'ruang'])->get();

        return view('pages.pegawai.form', compact([
            'bidangs',
            'pangkats',
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|integer|unique:pegawais,nip|unique:users,username',
            'nama' => 'required',
            'tipe' => 'required|in:pegawai,admin',
            'status' => 'required|in:aktif,non aktif',
            'password' => 'confirmed',
            'bidang_id' => 'required|exists:bidangs,id',
            'pangkat_golongan_id' => 'nullable|exists:pangkat_golongans,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = new User;
            $user->username = $request->nip;
            $user->password = $request->password ?? $request->nip;
            $user->save();

            $user->assignRole($request->tipe);

            $pegawai = new Pegawai;
            $pegawai->user_id = $user->id;
            $pegawai->tipe = $request->tipe;
            $pegawai->status = $request->status;
            $pegawai->nip = $request->nip;
            $pegawai->nama = $request->nama;
            $pegawai->pangkat_golongan_id = $request->pangkat_golongan_id;
            $pegawai->jabatan = $request->jabatan;
            $pegawai->peran = $request->peran;
            $pegawai->bidang_id = $request->bidang_id;
            $pegawai->save();
        });

        $request->session()->flash('success', 'Pegawai berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pegawai $pegawai)
    {
        return $pegawai;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $bidangs = Bidang::all();
        $pangkats = PangkatGolongan::select(['id', 'pangkat', 'golongan', 'ruang'])->get();

        return view('pages.pegawai.form', compact([
            'pegawai',
            'bidangs',
            'pangkats',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $user = $pegawai->user;

        $request->validate([
            'nip' => [
                'required',
                'integer',
                Rule::unique('pegawais', 'nip')->ignore($pegawai->id),
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'nama' => 'required',
            'tipe' => 'required|in:pegawai,admin',
            'status' => 'required|in:aktif,non aktif',
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

            $user->syncRoles([$request->tipe]);

            $pegawai->user_id = $user->id;
            $pegawai->tipe = $request->tipe;
            $pegawai->status = $request->status;
            $pegawai->nip = $request->nip;
            $pegawai->nama = $request->nama;
            $pegawai->pangkat_golongan_id = $request->pangkat_golongan_id;
            $pegawai->jabatan = $request->jabatan;
            $pegawai->peran = $request->peran;
            $pegawai->bidang_id = $request->bidang_id;
            $pegawai->save();
        });

        $request->session()->flash('success', 'Pegawai berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Pegawai $pegawai)
    {
        $pegawai->delete();
    }
}
