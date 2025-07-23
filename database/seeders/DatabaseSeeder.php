<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            BidangSeeder::class,
            PangkatGolonganSeeder::class,
            PegawaiSeeder::class,
            JenisPelatihanSeeder::class,
            DiklatSeeder::class,
            PpmSeeder::class,
            SeminarSeeder::class,
            WebinarSeeder::class,
            LcSeeder::class,
        ]);
    }
}
