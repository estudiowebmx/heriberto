<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('municipio')->nullable()->after('seccion');
            $table->string('distrito_local')->nullable()->after('municipio');
            $table->string('distrito_federal')->nullable()->after('distrito_local');
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn(['municipio', 'distrito_local', 'distrito_federal']);
        });
    }
};
