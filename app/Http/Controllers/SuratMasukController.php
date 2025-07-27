<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SuratMasuk;
use App\Models\Webinar;
use App\Service\FileService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SuratMasukController extends Controller
{
    public function __construct(private readonly FileService $fileService) {}

    public function datatable(Request $request)
    {
        $datas = SuratMasuk::query();

        if ($request->dari_tanggal) {
            $datas = $datas->where('tanggal_surat', '>=', $request->dari_tanggal);
        }

        if ($request->sampai_tanggal) {
            $datas = $datas->where('tanggal_surat', '<=', $request->sampai_tanggal);
        }

        return DataTables::of($datas)
            ->addColumn('berkas', function ($row) {
                return view('pages.surat-masuk.berkas', ['row' => $row])->render();
            })
            ->addColumn('action', function ($row) {
                return view('pages.surat-masuk.action', ['row' => $row])->render();
            })
            ->editColumn('tanggal_pelaksanaan', function ($row) {
                return date('d-m-Y', strtotime($row->tanggal_pelaksanaan));
            })
            ->rawColumns(['berkas', 'action'])
            ->make();
    }


    public function index(Request $request) {
        return view('pages.surat-masuk.index');
    }

    public function berkas(SuratMasuk $suratMasuk)
    {
        $fileName = 'Surat Masuk ' . $suratMasuk->id . '.pdf';

            return response()
                ->file(
                    storage_path('app/private/surat_masuk/'.$suratMasuk->id),
                    [
                        'Content-Type' => $this->fileService->getMimeTypeByFilename($fileName),
                        'Content-Disposition' => 'inline; filename="'.urlencode($fileName).'"',
                        // header untuk mematikan cache
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]
                );
    }
}
