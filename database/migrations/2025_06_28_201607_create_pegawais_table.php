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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bidang_id')->constrained();
            $table->foreignId('pangkat_golongan_id')->nullable()->constrained();
            $table->enum('tipe', ['pegawai', 'admin'])->default('pegawai');
            $table->enum('status', ['aktif', 'non aktif'])->default('aktif');
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('peran')->nullable();
            $table->timestamps();

            $table->index('tipe');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
