<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Ppm;
use Illuminate\Database\Seeder;

class PpmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::all()->each(function ($pegawai) {
            Ppm::factory(rand(1, 5))->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
