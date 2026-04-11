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
        Schema::create('departemen_terlibat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulir_id')->constrained('formulirs')->onDelete('cascade');
            $table->integer('urutan');
            $table->foreignId('departemen_id')->constrained('departemen');
            $table->timestamp('tanggal_diterima')->nullable();
            $table->foreignId('diterima_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_selesai')->nullable();
            $table->integer('qty');
            $table->foreignId('paraf_qc')->nullable()->constrained('users');
            $table->foreignId('paraf_spv')->nullable()->constrained('users');
            $table->json('data_tambahan')->nullable();
            $table->json('item_pemeriksaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemen_terlibat');
    }
};
