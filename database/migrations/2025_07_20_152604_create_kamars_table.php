<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Run the migrations.
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {
            $table->id('id_kamar');
            $table->string('nomor_kamar', 10)->unique();
            $table->unsignedBigInteger('id_tipe_kamar');
            $table->boolean('status_kamar')->default(true);
            $table->timestamps();
            $table->foreign('id_tipe_kamar')->references('id_tipe_kamar')->on('tipe_kamars')->onDelete('restrict');
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
