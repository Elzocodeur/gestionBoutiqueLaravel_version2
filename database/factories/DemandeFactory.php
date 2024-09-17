<?php

namespace Database\Factories;

use App\Models\Client;
use App\Enums\DemandeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;


class DemandeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'montant' => $this->faker->numberBetween(100, 10000),
            'date' => $this->faker->date(),
            'motif' => $this->faker->sentence(), 
            'etat' => $this->faker->randomElement([
                DemandeEnum::EN_COURS->value,
                DemandeEnum::ANNULER->value,
                DemandeEnum::VALIDER->value,
            ]), 
            'client_id' => Client::factory(), 
        ];
    }
}
