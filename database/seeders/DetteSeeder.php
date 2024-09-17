<?php

namespace Database\Seeders;

use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use Illuminate\Database\Seeder;

class DetteSeeder extends Seeder
{

    public function run(): void
    {
        //
        Client::all()->each(function ($client) {
            Dette::factory()
                ->count(3)
                ->create([
                    'client_id' => $client->id,
                ])
                ->each(function ($dette) {
                    $articles = Article::inRandomOrder()->take(rand(1, 5))->get();

                    foreach ($articles as $article) {
                        $dette->articles()->attach($article->id, [
                            'price' => $article->price,
                            'quantity' => rand(1, 10),
                        ]);
                    }
                });
        });
    }
}
