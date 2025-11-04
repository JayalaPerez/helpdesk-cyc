<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // category más flexible
        DB::statement("ALTER TABLE tickets MODIFY category VARCHAR(255) NULL");

        // department más flexible
        DB::statement("ALTER TABLE tickets MODIFY department VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // Aquí podrías revertir a ENUM si quisieras, pero lo dejamos vacío
    }
};
