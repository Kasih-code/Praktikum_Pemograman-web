<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kategoris')) {
            Schema::create('kategoris', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->timestamps();
                $table->engine = 'InnoDB';
            });

            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `kategoris` ENGINE = InnoDB');
        }

        if (! Schema::hasIndex('kategoris', ['nama'], 'unique')) {
            Schema::table('kategoris', function (Blueprint $table) {
                $table->unique('nama');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
