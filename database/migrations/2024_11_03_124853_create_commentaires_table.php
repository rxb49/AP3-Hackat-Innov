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
        Schema::create('COMMENTAIRES', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->Integer('idhackathon'); // Clé étrangère vers hackathon
            $table->string('libelle'); // Colonne libelle pour le commentaire

            // Définition de la clé étrangère
            $table->foreign('idhackathon')
                  ->references('idhackathon')
                  ->on('HACKATHON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};
