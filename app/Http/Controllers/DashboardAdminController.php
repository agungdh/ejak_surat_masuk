<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Diklat;
use App\Models\Lc;
use App\Models\Pegawai;
use App\Models\Ppm;
use App\Models\Seminar;
use App\Models\Webinar;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DashboardAdminController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $pegawai = Pegawai::count();
        $diklat = Diklat::where(DB::raw('year(dari_tanggal_pelaksanaan)'), date('Y'))->sum('jumlah_jam_pelatihan');
        $ppm = Ppm::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->sum('jumlah_jam_pelatihan');
        $seminar = Seminar::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->sum('jumlah_jam');
        $webinar = Webinar::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->sum('jumlah_jam');
        $lc = Lc::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->sum('jumlah_jam');

        return view('pages.dashboard-admin', compact([
            'pegawai',
            'diklat',
            'ppm',
            'seminar',
            'webinar',
            'lc',
        ]));
    }

    public function datatable(Request $request)
    {
        $tahun = date('Y');

        $datas = Pegawai::from((new Pegawai)->getTable().' as p')
            ->with(['diklats', 'ppms', 'seminars', 'webinars', 'lcs'])
            ->join((new Bidang)->getTable().' as b', 'p.bidang_id', '=', 'b.id')
            ->select(['p.*', 'b.bidang']);

        // Loop per triwulan, Q1=1–3, Q2=4–6, Q3=7–9, Q4=10–12
        for ($i = 1; $i <= 4; $i++) {
            $start = ($i - 1) * 3 + 1;
            $end = $i * 3;

            $datas = $datas->addSelect(DB::raw("(
            IFNULL((
                SELECT SUM(jumlah_jam_pelatihan)
                FROM diklats
                WHERE pegawai_id = p.id
                  AND YEAR(dari_tanggal_pelaksanaan) = {$tahun}
                  AND MONTH(dari_tanggal_pelaksanaan) BETWEEN {$start} AND {$end}
            ), 0)
            + IFNULL((
                SELECT SUM(jumlah_jam_pelatihan)
                FROM ppms
                WHERE pegawai_id = p.id
                  AND YEAR(tanggal_pelaksanaan) = {$tahun}
                  AND MONTH(tanggal_pelaksanaan) BETWEEN {$start} AND {$end}
            ), 0)
            + IFNULL((
                SELECT SUM(jumlah_jam)
                FROM seminars
                WHERE pegawai_id = p.id
                  AND YEAR(tanggal_pelaksanaan) = {$tahun}
                  AND MONTH(tanggal_pelaksanaan) BETWEEN {$start} AND {$end}
            ), 0)
            + IFNULL((
                SELECT SUM(jumlah_jam)
                FROM webinars
                WHERE pegawai_id = p.id
                  AND YEAR(tanggal_pelaksanaan) = {$tahun}
                  AND MONTH(tanggal_pelaksanaan) BETWEEN {$start} AND {$end}
            ), 0)
            + IFNULL((
                SELECT SUM(jumlah_jam)
                FROM lcs
                WHERE pegawai_id = p.id
                  AND YEAR(tanggal_pelaksanaan) = {$tahun}
                  AND MONTH(tanggal_pelaksanaan) BETWEEN {$start} AND {$end}
            ), 0)
        ) AS triwulan_{$i}"));
        }

        return DataTables::of($datas)
            ->editColumn('tipe', function ($row) {
                return ucwords($row->tipe);
            })
            ->editColumn('status', function ($row) {
                return ucwords($row->status);
            })
            ->editColumn('total_jp', function ($row) {
                return $row->triwulan_1
                    + $row->triwulan_2
                    + $row->triwulan_3
                    + $row->triwulan_4;
            })
            ->make();
    }
}
