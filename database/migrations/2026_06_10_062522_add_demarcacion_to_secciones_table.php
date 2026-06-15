<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->string('demarcacion')->nullable()->after('distrito_federal');
        });

        Schema::table('personas', function (Blueprint $table) {
            $table->string('demarcacion')->nullable()->after('distrito_federal');
        });
    }

    public function down(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->dropColumn('demarcacion');
        });

        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('demarcacion');
        });
    }
};
