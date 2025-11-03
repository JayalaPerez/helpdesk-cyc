<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_entries', function (Blueprint $table) {
            $table->string('eliminado_por')->nullable()->after('fecha_eliminacion');
        });
    }

    public function down(): void
    {
        Schema::table('password_entries', function (Blueprint $table) {
            $table->dropColumn('eliminado_por');
        });
    }
};
