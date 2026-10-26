<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('grupo_persona');
        Schema::dropIfExists('personas');
        Schema::dropIfExists('grupos');

        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('sexo', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('celular', 10)->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('clave_elector', 20)->nullable();
            $table->string('domicilio')->nullable();
            $table->string('colonia')->nullable();
            $table->string('localidad')->nullable();
            $table->string('seccion', 20)->nullable()->index();
            $table->string('promotor')->nullable();
            $table->timestamps();
        });

        Schema::create('grupo_persona', function (Blueprint $table) {
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->primary(['grupo_id', 'persona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_persona');
        Schema::dropIfExists('personas');
        Schema::dropIfExists('grupos');

        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('telefono');
            $table->string('seccion')->nullable();
            $table->string('promotor')->nullable();
            $table->string('municipio')->nullable();
            $table->string('distrito_local')->nullable();
            $table->string('distrito_federal')->nullable();
            $table->string('demarcacion')->nullable();
            $table->timestamps();
        });
    }
};
