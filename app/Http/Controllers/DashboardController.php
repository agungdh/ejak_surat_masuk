<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Diklat;
use App\Models\Lc;
use App\Models\PangkatGolongan;
use App\Models\Pegawai;
use App\Models\Ppm;
use App\Models\Seminar;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function rekapitulasiPegawaiData()
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

        return $datas->where('p.id', Auth::user()->pegawai->id)->first();
    }

    public function index(Request $request)
    {
        $diklat = Diklat::where(DB::raw('year(dari_tanggal_pelaksanaan)'), date('Y'))->where('pegawai_id', Auth::user()->pegawai->id)->sum('jumlah_jam_pelatihan');
        $ppm = Ppm::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->where('pegawai_id', Auth::user()->pegawai->id)->sum('jumlah_jam_pelatihan');
        $seminar = Seminar::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->where('pegawai_id', Auth::user()->pegawai->id)->sum('jumlah_jam');
        $webinar = Webinar::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->where('pegawai_id', Auth::user()->pegawai->id)->sum('jumlah_jam');
        $lc = Lc::where(DB::raw('year(tanggal_pelaksanaan)'), date('Y'))->where('pegawai_id', Auth::user()->pegawai->id)->sum('jumlah_jam');

        $rekapitulasiPegawaiData = $this->rekapitulasiPegawaiData();

        return view('pages.dashboard', compact([
            'diklat',
            'ppm',
            'seminar',
            'webinar',
            'lc',

            'rekapitulasiPegawaiData',
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
            $pegawai->peran = $request->peran;
            $pegawai->bidang_id = $request->bidang_id;
            $pegawai->save();
        });

        $request->session()->flash('success', 'Profil berhasil disimpan.');
    }
}
