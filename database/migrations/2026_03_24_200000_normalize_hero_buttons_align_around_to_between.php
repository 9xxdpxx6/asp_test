<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('hero_settings')->where('buttons_align', 'around')->update(['buttons_align' => 'between']);
    }

    public function down(): void
    {
        // Не восстанавливаем «around» — значение устарело.
    }
};
