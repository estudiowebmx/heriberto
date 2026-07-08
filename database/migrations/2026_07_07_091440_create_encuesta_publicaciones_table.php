<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_denuncia_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('consecutivo')->default(1);
            $table->string('red_social');
            $table->string('pagina');
            $table->date('fecha_publicacion');
            $table->string('enlace');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_publicaciones');
    }
};
