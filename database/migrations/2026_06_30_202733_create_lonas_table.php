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
        Schema::create('lonas', function (Blueprint $table) {
            $table->id();
            $table->string('denunciante_nombre');
            $table->string('denunciante_clave_elector');
            $table->string('denunciante_domicilio');
            $table->string('denunciante_email')->nullable();
            $table->string('denunciante_sexo', 1);
            $table->string('registered_by');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('dia_numero', 2);
            $table->string('mes', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lonas');
    }
};
