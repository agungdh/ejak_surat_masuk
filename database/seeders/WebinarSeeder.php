<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Webinar;
use Illuminate\Database\Seeder;

class WebinarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::all()->each(function ($pegawai) {
            Webinar::factory(rand(1, 5))->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
