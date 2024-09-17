<?php

namespace Database\Factories;

use App\Facades\RoleFacade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;


class UserFactory extends Factory
{

    public function definition()
    {
        return [
            'prenom' => $this->faker->firstName,
            'nom' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'role_id' => RoleFacade::getId('boutiquier'), 
            'is_blocked' => $this->faker->boolean,
            'photo_url' => "/storage/images/avatar.jpg",
        ];
    }


    public function admin()
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleFacade::getId('admin')
        ]);
    }


    public function client()
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleFacade::getId('client')
        ]);
    }


    public function boutiquier()
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => RoleFacade::getId('boutiquier')
        ]);
    }
}