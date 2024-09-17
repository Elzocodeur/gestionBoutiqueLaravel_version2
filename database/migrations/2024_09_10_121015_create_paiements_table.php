<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2); 
            $table->date('date');
            $table->foreignId('dette_id')->constrained('dettes')->onDelete('cascade'); 
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); 
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
