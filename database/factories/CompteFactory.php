<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compte>
 */
class CompteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_compte' => 'CPT' . $this->faker->unique()->numberBetween(100000, 999999),
            'type_compte' => $this->faker->randomElement(['cheque', 'epargne']),
            'devise' => 'CFA',
            'statut' => $this->faker->randomElement(['actif', 'bloque', 'ferme']),
            'version' => 1,
        ];
    }
}
