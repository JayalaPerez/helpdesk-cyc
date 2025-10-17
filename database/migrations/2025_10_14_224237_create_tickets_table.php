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
        Schema::create('tickets', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // users.id
    $table->string('subject');
    $table->string('department')->nullable();
    $table->enum('priority', ['Baja','Media','Alta','Crítica'])->default('Media');
    $table->enum('status', ['Nuevo','En Progreso','Resuelto','Cerrado'])->default('Nuevo');
    $table->text('description');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
