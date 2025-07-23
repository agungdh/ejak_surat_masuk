<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PangkatGolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Baca file CSV
        $filePath = base_path('dev/pangkat_golongan.csv');
        $csvData = array_map('str_getcsv', file($filePath));

        unset($csvData[0]);
        foreach ($csvData as $row) {
            DB::table('pangkat_golongans')->insert([
                'jenjang' => $row[1],
                'pangkat' => $row[2],
                'golongan' => $row[3],
                'ruang' => $row[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
