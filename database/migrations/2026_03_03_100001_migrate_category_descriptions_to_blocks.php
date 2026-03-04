<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Конвертирует существующие description категорий в блоки типа 'text'.
     */
    public function up(): void
    {
        $categories = DB::table('categories')
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->get();

        foreach ($categories as $category) {
            // Проверяем, есть ли уже блоки у этой категории
            $hasBlocks = DB::table('category_blocks')
                ->where('category_id', $category->id)
                ->exists();

            if (!$hasBlocks) {
                DB::table('category_blocks')->insert([
                    'category_id' => $category->id,
                    'type' => 'text',
                    'content' => json_encode(['html' => $category->description]),
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем все блоки, которые были созданы из description
        // (только те категории, у которых ровно один блок типа text)
        $categories = DB::table('categories')
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->get();

        foreach ($categories as $category) {
            $blockCount = DB::table('category_blocks')
                ->where('category_id', $category->id)
                ->count();

            if ($blockCount === 1) {
                DB::table('category_blocks')
                    ->where('category_id', $category->id)
                    ->where('type', 'text')
                    ->delete();
            }
        }
    }
};
