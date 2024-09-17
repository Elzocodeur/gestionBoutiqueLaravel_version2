<?php

namespace App\Console\Commands;

use App\Jobs\UploadFileJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RetryPhotoUploadCommand extends Command
{
    protected $signature = 'retry:upload';
    protected $description = 'Relance le chargement des photos des utilisateurs qui sont encore stockées localement';


    public function handle()
    {
        $users = User::where("is_photo_local", true)->get();
        if (!$users) {
            $this->info('Aucune photo locale à relancer.');
            return;
        }

        if($users instanceof Collection){
            foreach ($users as $user) {
                $this->retry($user);
            }
        }
        elseif($users instanceof User)
            $this->retry($users);

        $this->info('Relance du chargement des photos terminée.');
    }

    private function retry(User $user){
        if ($user->photo_url) {
            $this->info("Relance du chargement de la photo pour l'utilisateur : {$user->email}");
            UploadFileJob::dispatch($user, $user->photo_url);
        }
    }
}
