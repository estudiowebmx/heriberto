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
        Schema::create('lona_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lona_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('consecutivo')->default(1);
            $table->string('calle_y_num');
            $table->string('colonia');
            $table->string('condicion_inmueble');
            $table->string('photo_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lona_evidences');
    }
};
