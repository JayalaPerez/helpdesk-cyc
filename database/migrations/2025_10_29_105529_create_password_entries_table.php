<?php

// database/migrations/2025_10_28_000000_create_password_entries_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_entries', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('aplicacion');
            $table->string('estado')->default('Nuevo');
            $table->string('usuario')->nullable();
            $table->string('correo')->nullable();
            $table->text('password_encrypted');
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_eliminacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_entries');
    }
};
