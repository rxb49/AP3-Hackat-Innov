<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ADMINISTRATEUR', function (Blueprint $table) {
            $table->boolean('is_a2f_enabled')->default(true); // Par défaut, A2F est activée
        });
    }
    
    public function down()
    {
        Schema::table('ADMINISTRATEUR', function (Blueprint $table) {
            $table->dropColumn('is_a2f_enabled');
        });
    }
};
