<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ppm>
 */
class PpmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis_pelatihan_id' => fake()->numberBetween(1, 5),
            'nomor_surat' => fake()->unique()->numerify('PPM-###########'),
            'materi_pengembangan' => fake()->sentence(),
            'tanggal_pelaksanaan' => fake()->dateTimeThisYear(),
            'jumlah_jam_pelatihan' => fake()->numberBetween(1, 100),
        ];
    }
}
