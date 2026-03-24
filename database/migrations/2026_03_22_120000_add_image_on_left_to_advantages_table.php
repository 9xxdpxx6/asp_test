<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advantages', function (Blueprint $table) {
            $table->boolean('image_on_left')->default(true)->after('sort_order');
        });

        // Как на сайте: нечётные по порядку — фото слева, чётные — справа
        $rows = DB::table('advantages')->orderBy('sort_order')->orderBy('id')->get();

        foreach ($rows as $row) {
            $onLeft = ((int) $row->sort_order % 2) === 1;

            DB::table('advantages')->where('id', $row->id)->update([
                'image_on_left' => $onLeft,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('advantages', function (Blueprint $table) {
            $table->dropColumn('image_on_left');
        });
    }
};
