<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('profesion_ocupacion');
            $table->string('lugar_nacimiento');
            $table->text('domicilio_actual')->nullable();
            $table->text('hijos_info')->nullable();
            $table->string('padres_viven')->nullable();
            $table->string('padres_origen')->nullable();
            $table->string('padres_ocupacion')->nullable();
            $table->json('preocupaciones')->nullable();
            $table->string('problema_justicia')->nullable();
            $table->text('enfoque_gobernador')->nullable();
            $table->json('errores_gobierno')->nullable();
            $table->json('deficiencias_servicios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
