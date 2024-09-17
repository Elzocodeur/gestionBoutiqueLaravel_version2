<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClientSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Initialisation des roles
        Role::updateOrCreate(['libelle' => 'admin'], ['libelle' => 'admin']);
        Role::updateOrCreate(['libelle' => 'boutiquier'], ['libelle' => 'boutiquier']);
        Role::updateOrCreate(['libelle' => 'client'], ['libelle' => 'client']);
        
        // Initialisation des catégories
        Categorie::updateOrCreate(['libelle' => 'gold'], ['libelle' => 'gold']);
        Categorie::updateOrCreate(['libelle' => 'silver'], ['libelle' => 'silver']);
        Categorie::updateOrCreate(['libelle' => 'bronze'], ['libelle' => 'bronze']);


        $this->call([
            ClientSeeder::class,
            UserSeeder::class,
            ClientsUsersSeeder::class,
            ArticleSeeder::class,
            DetteSeeder::class,
            DemandeSeeder::class,
        ]);
    }
}
