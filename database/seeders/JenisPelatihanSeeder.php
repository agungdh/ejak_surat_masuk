<?php

namespace Database\Seeders;

use App\Models\JenisPelatihan;
use Illuminate\Database\Seeder;

class JenisPelatihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Penjenjangan',
            'MOOC',
            'Sertifikasi',
            'Teknis Substansif',
            'Pelatihan Kepemimpinan',
        ] as $jenisPelatihan) {
            $data = new JenisPelatihan;

            $data->jenis_pelatihan = $jenisPelatihan;

            $data->save();
        }
    }
}
