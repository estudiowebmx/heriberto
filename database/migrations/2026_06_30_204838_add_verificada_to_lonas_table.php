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
        Schema::table('lonas', function (Blueprint $table) {
            $table->boolean('verificada')->default(false)->after('mes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lonas', function (Blueprint $table) {
            $table->dropColumn('verificada');
        });
    }
};
