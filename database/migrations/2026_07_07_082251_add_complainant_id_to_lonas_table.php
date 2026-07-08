<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Lona;
use App\Models\Complainant;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lonas', function (Blueprint $table) {
            $table->foreignId('complainant_id')->nullable()->after('id');
        });

        Lona::whereNull('complainant_id')->each(function ($lona) {
            $complainant = Complainant::where('clave_elector', $lona->denunciante_clave_elector)->first();
            if ($complainant) {
                $lona->update(['complainant_id' => $complainant->id]);
            }
        });

        Schema::table('lonas', function (Blueprint $table) {
            $table->foreignId('complainant_id')->nullable(false)->change();
            $table->foreign('complainant_id')->references('id')->on('complainants')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lonas', function (Blueprint $table) {
            $table->dropForeign(['complainant_id']);
            $table->dropColumn('complainant_id');
        });
    }
};
