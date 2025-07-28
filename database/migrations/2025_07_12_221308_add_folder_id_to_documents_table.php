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
        Schema::table('documents', function (Blueprint $table) {
            // Tambahkan kolom folder_id dan relasi ke tabel folders
            $table->foreignId('folder_id')
                ->nullable()
                ->constrained('folders') // mengacu ke tabel 'folders'
                ->onDelete('cascade');   // jika folder dihapus, dokumen juga ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Hapus foreign key dan kolom
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
