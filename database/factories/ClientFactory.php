<?php

namespace Database\Factories;

use App\Facades\CategorieFacade;
use App\Facades\QrCodeFacade;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefixes = ['77', '78', '70', '76', '75'];
        $telephone = $this->faker->numerify($this->faker->randomElement($prefixes) . '#######');
        $qrCodePath = QrCodeFacade::generateQrCode($telephone);
        $prefixes = ['77', '78', '70', '76', '75'];
        $telephone = $this->faker->numerify($this->faker->randomElement($prefixes) . '#######');
        $qrCodePath = QrCodeFacade::generateQrCode($telephone);

        return [
            'surname' => $this->faker->unique()->name,
            'adresse' => $this->faker->address,
            'telephone' => $telephone,
            'qrcode' => $qrCodePath,
            'categorie_id' => CategorieFacade::getId("bronze"), 
        ];
    }

    public function gold()
    {
        return $this->state(fn(array $attributes) => [
            'categorie_id' => CategorieFacade::getId('gold')
        ]);
    }


    public function silver()
    {
        return $this->state(fn(array $attributes) => [
            'categorie_id' => CategorieFacade::getId('silver'),
            'max_montant' => $this->faker->numberBetween(1000, 50000)
        ]);
    }


    public function bronze()
    {
        return $this->state(fn(array $attributes) => [
            'categorie_id' => CategorieFacade::getId('bronze')
        ]);
    }
}