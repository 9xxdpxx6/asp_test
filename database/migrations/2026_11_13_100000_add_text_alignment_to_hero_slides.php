<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            // Выравнивание текста на слайде: по горизонтали (left|center|right) и по высоте (top|center|bottom)
            $table->string('text_align', 8)->default('left');
            $table->string('text_valign', 8)->default('center');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['text_align', 'text_valign']);
        });
    }
};
