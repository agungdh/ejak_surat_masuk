<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'KEPALA PERWAKILAN',
            'KEPALA BAGIAN UMUM',
            'SUBBAGIAN KEPEGAWAIAN',
            'SUBBAGIAN KEUANGAN',
            'SUBBAGIAN UMUM',
            'BIDANG PENGAWASAN INSTANSI PEMERINTAH PUSAT',
            'BIDANG AKUNTABILITAS PEMERINTAH DAERAH',
            'BIDANG AKUNTAN NEGARA',
            'BIDANG INVESTIGASI',
            'BIDANG PROGRAM DAN PELAPORAN SERTA PEMBINAAN JFA',
        ] as $bidang) {
            $data = new Bidang;

            $data->bidang = $bidang;

            $data->save();
        }
    }
}
