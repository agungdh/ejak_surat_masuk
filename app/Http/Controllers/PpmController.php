<?php

namespace App\Http\Controllers;

use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use App\Models\Ppm;
use App\Service\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PpmController extends Controller
{
    public function __construct(private readonly FileService $fileService) {}

    public function datatable(Request $request)
    {
        $datas = Ppm::from((new Ppm)->getTable().' as d');
        $datas->select([
            'd.*',
            'p.nama',
            'p.nip',
            'jp.jenis_pelatihan',
        ]);
        $datas = $datas->join((new Pegawai)->getTable().' as p', 'd.pegawai_id', '=', 'p.id');
        $datas = $datas->join((new JenisPelatihan)->getTable().' as jp', 'd.jenis_pelatihan_id', '=', 'jp.id');

        if ($request->pegawai_id) {
            $datas = $datas->where('p.id', $request->pegawai_id);
        }

        if ($request->jenis_pelatihan_id) {
            $datas = $datas->where('jp.id', $request->jenis_pelatihan_id);
        }

        if ($request->dari_tanggal) {
            $datas = $datas->where('d.tanggal_pelaksanaan', '>=', $request->dari_tanggal);
        }

        if ($request->sampai_tanggal) {
            $datas = $datas->where('d.tanggal_pelaksanaan', '<=', $request->sampai_tanggal);
        }

        if (auth()->user()->rolename === 'pegawai') {
            $datas = $datas->where('p.id', auth()->user()->pegawai->id);
        }

        return DataTables::of($datas)
            ->addColumn('action', function ($row) {
                return view('pages.ppm.action', ['row' => $row])->render();
            })
            ->editColumn('tanggal_pelaksanaan', function ($row) {
                return date('d-m-Y', strtotime($row->tanggal_pelaksanaan));
            })
            ->make();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();
        $jenisPelatihans = JenisPelatihan::select(['id', 'jenis_pelatihan'])->get();

        return view('pages.ppm.index', compact([
            'pegawais',
            'jenisPelatihans',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();
        $jenisPelatihans = JenisPelatihan::select(['id', 'jenis_pelatihan'])->get();

        return view('pages.ppm.form', compact([
            'pegawais',
            'jenisPelatihans',
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->rolename === 'pegawai') {
            $request->merge([
                'pegawai_id' => $request->user()->pegawai->id,
            ]);
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'nomor_surat' => 'required',
            'materi_pengembangan' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'jumlah_jam_pelatihan' => 'required|integer',
        ]);

        $ppm = new Ppm;
        $ppm->pegawai_id = $request->pegawai_id;
        $ppm->jenis_pelatihan_id = $request->jenis_pelatihan_id;
        $ppm->nomor_surat = $request->nomor_surat;
        $ppm->materi_pengembangan = $request->materi_pengembangan;
        $ppm->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
        $ppm->jumlah_jam_pelatihan = $request->jumlah_jam_pelatihan;
        $ppm->save();

        $request->session()->flash('success', 'Ppm berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ppm $ppm)
    {
        return $ppm;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ppm $ppm)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();
        $jenisPelatihans = JenisPelatihan::select(['id', 'jenis_pelatihan'])->get();

        return view('pages.ppm.form', compact([
            'pegawais',
            'jenisPelatihans',
            'ppm',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ppm $ppm)
    {
        if ($request->user()->rolename === 'pegawai') {
            $request->merge([
                'pegawai_id' => $request->user()->pegawai->id,
            ]);
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'nomor_surat' => 'required',
            'materi_pengembangan' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'jumlah_jam_pelatihan' => 'required|integer',
        ]);

        $ppm->pegawai_id = $request->pegawai_id;
        $ppm->jenis_pelatihan_id = $request->jenis_pelatihan_id;
        $ppm->nomor_surat = $request->nomor_surat;
        $ppm->materi_pengembangan = $request->materi_pengembangan;
        $ppm->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
        $ppm->jumlah_jam_pelatihan = $request->jumlah_jam_pelatihan;
        $ppm->save();

        $request->session()->flash('success', 'Ppm berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Ppm $ppm)
    {
        $ppm->delete();

        Storage::delete('/ppm/'.$ppm->id);
    }
}
