<?php

namespace Database\Seeders;

use App\Models\Lc;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class LcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::all()->each(function ($pegawai) {
            Lc::factory(rand(1, 5))->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
