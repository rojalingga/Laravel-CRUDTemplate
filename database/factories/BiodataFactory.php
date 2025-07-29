<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Biodata>
 */
class BiodataFactory extends Factory
{
    protected $model = \App\Models\Biodata::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected static ?string $locale = 'id_ID';

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        return [
            'nama' => $faker->name(),
            'jenis_kelamin' => $faker->randomElement([1, 2]),
            'tgl_lahir' => $faker->date('Y-m-d', '2010-01-01'),
            'foto' => '',
        ];
    }
}
