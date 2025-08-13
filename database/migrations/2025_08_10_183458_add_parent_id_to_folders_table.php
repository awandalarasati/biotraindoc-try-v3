<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            if (!Schema::hasColumn('folders', 'parent_id')) {
                $table->foreignId('parent_id')
                      ->nullable()
                      ->constrained('folders')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            if (Schema::hasColumn('folders', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
        });
    }
};
