<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_denuncias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complainant_id')->constrained()->restrictOnDelete();
            $table->string('registered_by');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('dia_numero', 2);
            $table->string('mes', 20);
            $table->boolean('verificada')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_denuncias');
    }
};
