<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsUsersSeeder extends Seeder
{
   
    public function run(): void
    {
        User::factory(3)->client()->create()->each(function ($user) {
            $client = Client::factory()->makeOne();
            $user->client()->save($client);
        });
    }
}
