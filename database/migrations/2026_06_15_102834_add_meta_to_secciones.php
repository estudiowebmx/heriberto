<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->integer('meta')->default(0)->after('demarcacion');
        });

        DB::table('secciones')->get()->each(function ($s) {
            DB::table('secciones')->where('id', $s->id)->update(['meta' => rand(5, 20)]);
        });
    }

    public function down(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
