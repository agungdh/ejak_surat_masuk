<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Webinar;
use App\Service\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class WebinarController extends Controller
{
    public function __construct(private readonly FileService $fileService) {}

    public function datatable(Request $request)
    {
        $datas = Webinar::from((new Webinar)->getTable().' as w');
        $datas->select([
            'w.*',
            'p.nama',
            'p.nip',
        ]);
        $datas = $datas->join((new Pegawai)->getTable().' as p', 'w.pegawai_id', '=', 'p.id');

        if ($request->pegawai_id) {
            $datas = $datas->where('p.id', $request->pegawai_id);
        }

        if ($request->dari_tanggal) {
            $datas = $datas->where('w.tanggal_pelaksanaan', '>=', $request->dari_tanggal);
        }

        if ($request->sampai_tanggal) {
            $datas = $datas->where('w.tanggal_pelaksanaan', '<=', $request->sampai_tanggal);
        }

        if (auth()->user()->rolename === 'pegawai') {
            $datas = $datas->where('p.id', auth()->user()->pegawai->id);
        }

        return DataTables::of($datas)
            ->addColumn('berkas', function ($row) {
                return view('pages.webinar.berkas', ['row' => $row])->render();
            })
            ->addColumn('action', function ($row) {
                return view('pages.webinar.action', ['row' => $row])->render();
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

        return view('pages.webinar.index', compact([
            'pegawais',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();

        return view('pages.webinar.form', compact([
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
            $webinar = new Webinar;
            $webinar->pegawai_id = $request->pegawai_id;
            $webinar->materi_pengembangan = $request->materi_pengembangan;
            $webinar->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $webinar->jumlah_jam = $request->jumlah_jam;
            $webinar->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'webinar', $webinar->id
                );

                $webinar->filename = $request->berkas->getClientOriginalName();
                $webinar->save();
            }
        });

        $request->session()->flash('success', 'Webinar berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Webinar $webinar)
    {
        return $webinar;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Webinar $webinar)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();

        return view('pages.webinar.form', compact([
            'pegawais',
            'webinar',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Webinar $webinar)
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

        DB::transaction(function () use ($request, $webinar) {
            $webinar->pegawai_id = $request->pegawai_id;
            $webinar->materi_pengembangan = $request->materi_pengembangan;
            $webinar->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $webinar->jumlah_jam = $request->jumlah_jam;
            $webinar->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'webinar', $webinar->id
                );

                $webinar->filename = $request->berkas->getClientOriginalName();
                $webinar->save();
            }
        });

        $request->session()->flash('success', 'Webinar berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Webinar $webinar)
    {
        $webinar->delete();

        Storage::delete('/webinar/'.$webinar->id);
    }

    public function berkas(Webinar $webinar)
    {
        if ($webinar->filename) {
            return response()
                ->file(
                    storage_path('app/private/webinar/'.$webinar->id),
                    [
                        'Content-Type' => $this->fileService->getMimeTypeByFilename($webinar->filename),
                        'Content-Disposition' => 'inline; filename="'.urlencode($webinar->filename).'"',
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
