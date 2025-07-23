<?php

namespace App\Http\Controllers;

use App\Models\Diklat;
use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use App\Service\FileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DiklatController extends Controller
{
    public function __construct(private readonly FileService $fileService) {}

    public function datatable(Request $request)
    {
        $datas = Diklat::from((new Diklat)->getTable().' as d');
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
            $datas = $datas->where(function (Builder $query) use ($request) {
                $query->orWhere('d.dari_tanggal_pelaksanaan', '>=', $request->dari_tanggal);
                $query->orWhere('d.sampai_tanggal_pelaksanaan', '>=', $request->dari_tanggal);
            });
        }

        if ($request->sampai_tanggal) {
            $datas = $datas->where(function (Builder $query) use ($request) {
                $query->orWhere('d.dari_tanggal_pelaksanaan', '<=', $request->sampai_tanggal);
                $query->orWhere('d.sampai_tanggal_pelaksanaan', '<=', $request->sampai_tanggal);
            });
        }

        if (auth()->user()->rolename === 'pegawai') {
            $datas = $datas->where('p.id', auth()->user()->pegawai->id);
        }

        return DataTables::of($datas)
            ->addColumn('berkas', function ($row) {
                return view('pages.diklat.berkas', ['row' => $row])->render();
            })
            ->addColumn('action', function ($row) {
                return view('pages.diklat.action', ['row' => $row])->render();
            })
            ->editColumn('dari_tanggal_pelaksanaan', function ($row) {
                return date('d-m-Y', strtotime($row->dari_tanggal_pelaksanaan));
            })
            ->editColumn('sampai_tanggal_pelaksanaan', function ($row) {
                return date('d-m-Y', strtotime($row->sampai_tanggal_pelaksanaan));
            })
            ->rawColumns(['berkas', 'action'])
            ->make();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();
        $jenisPelatihans = JenisPelatihan::select(['id', 'jenis_pelatihan'])->get();

        return view('pages.diklat.index', compact([
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

        return view('pages.diklat.form', compact([
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
            'dari_tanggal_pelaksanaan' => 'required|date|before_or_equal:sampai_tanggal_pelaksanaan',
            'sampai_tanggal_pelaksanaan' => 'required|date|after_or_equal:dari_tanggal_pelaksanaan',
            'jumlah_jam_pelatihan' => 'required|integer',
            'berkas' => 'nullable|file',
        ]);

        DB::transaction(function () use ($request) {
            $diklat = new Diklat;
            $diklat->pegawai_id = $request->pegawai_id;
            $diklat->jenis_pelatihan_id = $request->jenis_pelatihan_id;
            $diklat->nomor_surat = $request->nomor_surat;
            $diklat->materi_pengembangan = $request->materi_pengembangan;
            $diklat->dari_tanggal_pelaksanaan = $request->dari_tanggal_pelaksanaan;
            $diklat->sampai_tanggal_pelaksanaan = $request->sampai_tanggal_pelaksanaan;
            $diklat->jumlah_jam_pelatihan = $request->jumlah_jam_pelatihan;
            $diklat->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'diklat', $diklat->id
                );

                $diklat->filename = $request->berkas->getClientOriginalName();
                $diklat->save();
            }
        });

        $request->session()->flash('success', 'Diklat berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Diklat $diklat)
    {
        return $diklat;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diklat $diklat)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();
        $jenisPelatihans = JenisPelatihan::select(['id', 'jenis_pelatihan'])->get();

        return view('pages.diklat.form', compact([
            'pegawais',
            'jenisPelatihans',
            'diklat',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diklat $diklat)
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
            'dari_tanggal_pelaksanaan' => 'required|date|before_or_equal:sampai_tanggal_pelaksanaan',
            'sampai_tanggal_pelaksanaan' => 'required|date|after_or_equal:dari_tanggal_pelaksanaan',
            'jumlah_jam_pelatihan' => 'required|integer',
            'berkas' => 'nullable|file',
        ]);

        DB::transaction(function () use ($request, $diklat) {
            $diklat->pegawai_id = $request->pegawai_id;
            $diklat->jenis_pelatihan_id = $request->jenis_pelatihan_id;
            $diklat->nomor_surat = $request->nomor_surat;
            $diklat->materi_pengembangan = $request->materi_pengembangan;
            $diklat->dari_tanggal_pelaksanaan = $request->dari_tanggal_pelaksanaan;
            $diklat->sampai_tanggal_pelaksanaan = $request->sampai_tanggal_pelaksanaan;
            $diklat->jumlah_jam_pelatihan = $request->jumlah_jam_pelatihan;
            $diklat->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'diklat', $diklat->id
                );

                $diklat->filename = $request->berkas->getClientOriginalName();
                $diklat->save();
            }
        });

        $request->session()->flash('success', 'Diklat berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Diklat $diklat)
    {
        $diklat->delete();

        Storage::delete('/diklat/'.$diklat->id);
    }

    public function berkas(Diklat $diklat)
    {
        if ($diklat->filename) {
            return response()
                ->file(
                    storage_path('app/private/diklat/'.$diklat->id),
                    [
                        'Content-Type' => $this->fileService->getMimeTypeByFilename($diklat->filename),
                        'Content-Disposition' => 'inline; filename="'.urlencode($diklat->filename).'"',
                        // header untuk mematikan cache
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]
                );
        }

        abort(404);
    }
}
