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
        Schema::create('ppms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained();
            $table->foreignId('jenis_pelatihan_id')->constrained();
            $table->string('nomor_surat');
            $table->string('materi_pengembangan');
            $table->date('tanggal_pelaksanaan');
            $table->integer('jumlah_jam_pelatihan');
            $table->timestamps();

            $table->index('nomor_surat');
            $table->index('tanggal_pelaksanaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppms');
    }
};
