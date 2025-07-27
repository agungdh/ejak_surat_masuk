<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::factory(50)->create();

        $roles = collect(['staf tu', 'kasubbag up', 'sekretaris', 'kadis']);
        $roles->each(function ($role) {
            $pegawai = Pegawai::factory()->create();

            $user = $pegawai->user;

            $user->username = 'ejak '.$role;
            $user->save();

            $user->syncRoles([$role]);

            $pegawai->tipe = $role;
            $pegawai->save();
        });
    }
}
