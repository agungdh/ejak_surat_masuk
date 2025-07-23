<?php

namespace App\Http\Controllers;

use App\Models\Lc;
use App\Models\Pegawai;
use App\Service\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LcController extends Controller
{
    public function __construct(private readonly FileService $fileService) {}

    public function datatable(Request $request)
    {
        $datas = Lc::from((new Lc)->getTable().' as l');
        $datas->select([
            'l.*',
            'p.nama',
            'p.nip',
        ]);
        $datas = $datas->join((new Pegawai)->getTable().' as p', 'l.pegawai_id', '=', 'p.id');

        if ($request->pegawai_id) {
            $datas = $datas->where('p.id', $request->pegawai_id);
        }

        if ($request->dari_tanggal) {
            $datas = $datas->where('l.tanggal_pelaksanaan', '>=', $request->dari_tanggal);
        }

        if ($request->sampai_tanggal) {
            $datas = $datas->where('l.tanggal_pelaksanaan', '<=', $request->sampai_tanggal);
        }

        if (auth()->user()->rolename === 'pegawai') {
            $datas = $datas->where('p.id', auth()->user()->pegawai->id);
        }

        return DataTables::of($datas)
            ->addColumn('berkas', function ($row) {
                return view('pages.lc.berkas', ['row' => $row])->render();
            })
            ->addColumn('action', function ($row) {
                return view('pages.lc.action', ['row' => $row])->render();
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

        return view('pages.lc.index', compact([
            'pegawais',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();

        return view('pages.lc.form', compact([
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
            $lc = new Lc;
            $lc->pegawai_id = $request->pegawai_id;
            $lc->materi_pengembangan = $request->materi_pengembangan;
            $lc->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $lc->jumlah_jam = $request->jumlah_jam;
            $lc->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'lc', $lc->id
                );

                $lc->filename = $request->berkas->getClientOriginalName();
                $lc->save();
            }
        });

        $request->session()->flash('success', 'Lc berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lc $lc)
    {
        return $lc;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lc $lc)
    {
        $pegawais = Pegawai::select(['id', 'nama', 'nip'])->orderBy('nip')->get();

        return view('pages.lc.form', compact([
            'pegawais',
            'lc',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lc $lc)
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

        DB::transaction(function () use ($request, $lc) {
            $lc->pegawai_id = $request->pegawai_id;
            $lc->materi_pengembangan = $request->materi_pengembangan;
            $lc->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $lc->jumlah_jam = $request->jumlah_jam;
            $lc->save();

            if ($request->hasFile('berkas')) {
                $request->file('berkas')->storeAs(
                    'lc', $lc->id
                );

                $lc->filename = $request->berkas->getClientOriginalName();
                $lc->save();
            }
        });

        $request->session()->flash('success', 'Lc berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Lc $lc)
    {
        $lc->delete();

        Storage::delete('/lc/'.$lc->id);
    }

    public function berkas(Lc $lc)
    {
        if ($lc->filename) {
            return response()
                ->file(
                    storage_path('app/private/lc/'.$lc->id),
                    [
                        'Content-Type' => $this->fileService->getMimeTypeByFilename($lc->filename),
                        'Content-Disposition' => 'inline; filename="'.urlencode($lc->filename).'"',
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
