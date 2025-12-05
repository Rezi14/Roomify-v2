<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Run the migrations.
    public function up(): void
    {
        Schema::create('tipe_kamars', function (Blueprint $table) {
            $table->id('id_tipe_kamar');
            $table->string('nama_tipe_kamar', 255)->unique();
            $table->decimal('harga_per_malam', 10, 2);
            $table->integer('kapasitas')->default(2);
            $table->text('deskripsi')->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('tipe_kamars');
    }
};
