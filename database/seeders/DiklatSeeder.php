<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class DiklatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::all()->each(function ($pegawai) {
            Diklat::factory(rand(1, 5))->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
