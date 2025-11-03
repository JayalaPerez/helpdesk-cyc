<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // closed_at
        if (!Schema::hasColumn('tickets', 'closed_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->timestamp('closed_at')->nullable()->after('status');
            });
        }

        // assigned_user_id (opcional, por si aún no está)
        if (!Schema::hasColumn('tickets', 'assigned_user_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('assigned_user_id')->nullable()
                      ->constrained('users')->nullOnDelete()->after('user_id');
            });
        }

        // category (opcional, por si aún no está)
        if (!Schema::hasColumn('tickets', 'category')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('category')->nullable()->after('department');
                // Si prefieres enum, podemos cambiarlo luego.
            });
        }
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'closed_at')) {
                $table->dropColumn('closed_at');
            }
            if (Schema::hasColumn('tickets', 'assigned_user_id')) {
                $table->dropForeign(['assigned_user_id']);
                $table->dropColumn('assigned_user_id');
            }
            if (Schema::hasColumn('tickets', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
