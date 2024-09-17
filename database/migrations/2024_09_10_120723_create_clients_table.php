<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('surname')->unique();
            $table->string('adresse');
            $table->string('telephone')->unique();
            $table->string('qrcode')->nullable(); 
            $table->integer('max_montant')->default(0); 
            $table->foreignId('user_id')->nullable()->unique()->constrained()->onDelete('set null');
            $table->foreignId('categorie_id')->constrained('categories');
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
