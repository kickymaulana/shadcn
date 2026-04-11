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
        Schema::create('formulirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sampel_id')->constrained('samples')->onDelete('cascade');
            $table->string('size');
            $table->integer('qty_sampel_kirim');
            $table->integer('running_ke');
            $table->date('tanggal_permintaan');
            $table->enum('status', ['Draft', 'Proses', 'Selesai', 'Ditolak'])->default('Draft');
            $table->foreignId('diperiksa_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulirs');
    }
};
