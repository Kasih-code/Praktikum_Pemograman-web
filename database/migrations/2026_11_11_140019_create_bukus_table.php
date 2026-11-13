<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bukus')) {
            Schema::create('bukus', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 10)->unique();
                $table->string('judul');
                $table->foreignId('kategori_id')->constrained('kategoris');
                $table->timestamps();
                $table->engine = 'InnoDB';
            });

            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `bukus` ENGINE = InnoDB');
        }

        if (! Schema::hasForeignKey('bukus', ['kategori_id'])) {
            Schema::table('bukus', function (Blueprint $table) {
                $table->foreign('kategori_id')->references('id')->on('kategoris');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
