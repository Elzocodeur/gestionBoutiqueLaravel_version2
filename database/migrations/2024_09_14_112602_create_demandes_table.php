<?php

use App\Enums\DemandeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->integer('montant');
            $table->date('date');
            $table->text('motif')->nullable();
            $table->enum('etat', [
                DemandeEnum::EN_COURS->value,
                DemandeEnum::ANNULER->value,
                DemandeEnum::VALIDER->value,
            ])->default(DemandeEnum::EN_COURS->value);

            $table->foreignId('client_id')->constrained('clients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
