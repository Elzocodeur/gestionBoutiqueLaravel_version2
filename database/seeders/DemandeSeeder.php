<?php

namespace Database\Seeders;

use App\Models\Demande;
use App\Models\Client;
use App\Models\Article;
use Illuminate\Database\Seeder;

class DemandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::all()->each(function ($client) {
            Demande::factory()
                ->count(1)
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
