<?php

namespace Database\Factories;

use App\Models\Bidang;
use App\Models\PangkatGolongan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = Role::inRandomOrder()->first();

        $user = User::factory()->create();
        $user->assignRole($role);

        $pangkatGolongan = PangkatGolongan::inRandomOrder()->first();

        $bidang = Bidang::inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'bidang_id' => $bidang->id,
            'pangkat_golongan_id' => $pangkatGolongan->id,
            'tipe' => $role->name,
            'status' => 'aktif',
            'nip' => $this->faker->unique()->numerify('#############'),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->jobTitle(),
        ];
    }
}
