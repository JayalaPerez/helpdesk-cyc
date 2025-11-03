<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_audits', function (Blueprint $table) {
            $table->id();

            // ID del ticket eliminado
            $table->unsignedBigInteger('ticket_id')->index();

            // ID del usuario que eliminó (admin)
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            // snapshot de los datos principales del ticket al momento de borrarlo
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('department')->nullable();
            $table->string('category')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();             // quién creó el ticket
            $table->unsignedBigInteger('assigned_user_id')->nullable();    // quién estaba asignado

            // timestamps originales del ticket
            $table->timestamp('ticket_created_at')->nullable();
            $table->timestamp('ticket_closed_at')->nullable();

            // Cuándo se hizo la eliminación
            $table->timestamp('deleted_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_audits');
    }
};
