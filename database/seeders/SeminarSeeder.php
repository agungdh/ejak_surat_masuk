<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Seminar;
use Illuminate\Database\Seeder;

class SeminarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::all()->each(function ($pegawai) {
            Seminar::factory(rand(1, 5))->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
