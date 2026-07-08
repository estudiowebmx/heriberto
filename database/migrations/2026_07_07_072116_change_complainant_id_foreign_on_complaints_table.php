<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['complainant_id']);
            $table->foreign('complainant_id')->references('id')->on('complainants')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['complainant_id']);
            $table->foreign('complainant_id')->references('id')->on('complainants')->cascadeOnDelete();
        });
    }
};
