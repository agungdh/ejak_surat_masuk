<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Seminar;
use App\Service\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SeminarController extends Controller
{
    public function __construct(private readonly FileService $fileService) {}

    public function datatable(Request $request)
    {
        $datas = Seminar::from((new Seminar)->getTable().' as s');
        $datas->select([
            's.*',
            'p.nama',
            'p.nip',
        ]);
        $datas = $datas->join((new Pegawai)->getTable().' as p', 's.pegawai_id', '=', 'p.id');

        if ($request->pegawai_id) {
            $datas = $datas->where('p.id', $request->pegawai_id);
        }

        if ($request->dari_tanggal) {
            $datas = $datas->where('s.tanggal_pelaksanaan', '>=', $request->dari_tanggal);
        }

        if ($request->sampai_tanggal) {
            $datas = $datas->where('s.tanggal_pelaksanaan', '<=', $request->sampai_tanggal);
        }

        if (auth()->user()->rolename === 'pegawai') {
            $datas = $datas->where('p.id', auth()->user()->pegawai->id);
        }

        return DataTables::of($datas)
            ->addColumn('berkas', function ($row) {
                return view('pages.seminar.berkas', ['row' => $row])->render();
            })
            ->addColumn('action', function ($row) {
                return view('pages.seminar.action', ['row' => $row])->render();
            })
            ->editColumn('tanggal_pelaksanaan', function ($row) {
                return date('d-m-Y', strtotime($row->tanggal_pelaksanaan));
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

        return view('pages.seminar.index', compact([
            'pegawais',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();

        return view('pages.seminar.form', compact([
            'pegawais',
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
            'materi_pengembangan' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'jumlah_jam' => 'required|integer',
            'berkas' => 'nullable|file',
        ]);

        DB::transaction(function () use ($request) {
            $seminar = new Seminar;
            $seminar->pegawai_id = $request->pegawai_id;
            $seminar->materi_pengembangan = $request->materi_pengembangan;
            $seminar->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $seminar->jumlah_jam = $request->jumlah_jam;
            $seminar->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'seminar', $seminar->id
                );

                $seminar->filename = $request->berkas->getClientOriginalName();
                $seminar->save();
            }
        });

        $request->session()->flash('success', 'Seminar berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Seminar $seminar)
    {
        return $seminar;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seminar $seminar)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();

        return view('pages.seminar.form', compact([
            'pegawais',
            'seminar',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seminar $seminar)
    {
        if ($request->user()->rolename === 'pegawai') {
            $request->merge([
                'pegawai_id' => $request->user()->pegawai->id,
            ]);
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'materi_pengembangan' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'jumlah_jam' => 'required|integer',
            'berkas' => 'nullable|file',
        ]);

        DB::transaction(function () use ($request, $seminar) {
            $seminar->pegawai_id = $request->pegawai_id;
            $seminar->materi_pengembangan = $request->materi_pengembangan;
            $seminar->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $seminar->jumlah_jam = $request->jumlah_jam;
            $seminar->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'seminar', $seminar->id
                );

                $seminar->filename = $request->berkas->getClientOriginalName();
                $seminar->save();
            }
        });

        $request->session()->flash('success', 'Seminar berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Seminar $seminar)
    {
        $seminar->delete();

        Storage::delete('/seminar/'.$seminar->id);
    }

    public function berkas(Seminar $seminar)
    {
        if ($seminar->filename) {
            return response()
                ->file(
                    storage_path('app/private/seminar/'.$seminar->id),
                    [
                        'Content-Type' => $this->fileService->getMimeTypeByFilename($seminar->filename),
                        'Content-Disposition' => 'inline; filename="'.urlencode($seminar->filename).'"',
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
