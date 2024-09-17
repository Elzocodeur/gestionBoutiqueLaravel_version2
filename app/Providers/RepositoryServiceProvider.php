<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\MongoArchiveDetteRepository;
use App\Repository\FirebaseArchiveDetteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repository\Interfaces\RoleRepositoryInterface::class, \App\Repository\RoleRepository::class);
        $this->app->bind(\App\Repository\Interfaces\CategorieRepositoryInterface::class, \App\Repository\CategorieRepository::class);
        $this->app->bind(\App\Repository\Interfaces\ClientRepositoryInterface::class, \App\Repository\ClientRepository::class);
        $this->app->bind(\App\Repository\Interfaces\UserRepositoryInterface::class, \App\Repository\UserRepository::class);
        $this->app->bind(\App\Repository\Interfaces\DetteRepositoryInterface::class, \App\Repository\DetteRepository::class);
        $this->app->bind(\App\Repository\Interfaces\ArticleRepositoryInterface::class, \App\Repository\ArticleRepository::class);
        $this->app->bind(\App\Repository\Interfaces\PaiementRepositoryInterface::class, \App\Repository\PaiementRepository::class);
        $this->app->bind(\App\Repository\Interfaces\DemandeRepositoryInterface::class, \App\Repository\DemandeRepository::class);

        $this->app->bind(\App\Repository\FirebaseArchiveDetteRepository::class, function ($app) {
            return new FirebaseArchiveDetteRepository(
                $app->make(\Kreait\Firebase\Contract\Database::class)
            );
        });
        $this->app->bind(\App\Repository\MongoArchiveDetteRepository::class, function ($app) {
            return new MongoArchiveDetteRepository(
                $app->make(\MongoDB\Client::class)
            );
        });
        $this->app->bind(\App\Repository\Interfaces\ArchiveDetteRepositoryInterface::class, function ($app) {
            if (config('database.archive') === 'firebase') {
                return $app->make(FirebaseArchiveDetteRepository::class);
            }
            return $app->make(MongoArchiveDetteRepository::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
