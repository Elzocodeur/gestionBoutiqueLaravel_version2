<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dette>
 */
class DetteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $echeance = Carbon::now()->addDays(3); // Ajoute 3 jours à la date actuelle

        return [
            'montant' => $this->faker->numberBetween(100, 1000),
            'client_id' => Client::factory(),
            'date' => $this->faker->date(),
            'echeance' => $echeance,
        ];
    }
}
