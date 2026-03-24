<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('slug');
            $table->boolean('show_on_home')->default(false);
            $table->unsignedSmallInteger('home_sort_order')->nullable();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE discounts MODIFY percentage DECIMAL(5,2) NULL');
            DB::statement('ALTER TABLE discounts MODIFY description LONGTEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE discounts ALTER COLUMN percentage DROP NOT NULL');
            DB::statement('ALTER TABLE discounts ALTER COLUMN description DROP NOT NULL');
        }

        Schema::create('discount_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('discounts')->onDelete('cascade');
            $table->string('type');
            $table->json('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['discount_id', 'sort_order']);
        });

        $now = now();
        $discounts = DB::table('discounts')->orderBy('id')->get();

        foreach ($discounts as $d) {
            $desc = $d->description ?? null;
            if ($desc !== null && trim(strip_tags($desc)) !== '') {
                DB::table('discount_blocks')->insert([
                    'discount_id' => $d->id,
                    'type' => 'text',
                    'content' => json_encode(['html' => $desc], JSON_UNESCAPED_UNICODE),
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $i = 0;
        foreach ($discounts as $d) {
            DB::table('discounts')->where('id', $d->id)->update([
                'show_on_home' => $i < 2,
                'home_sort_order' => $i < 2 ? $i + 1 : null,
            ]);
            $i++;
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_blocks');

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'show_on_home', 'home_sort_order']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('UPDATE discounts SET percentage = 0 WHERE percentage IS NULL');
            DB::statement('ALTER TABLE discounts MODIFY percentage DECIMAL(5,2) NOT NULL');
            DB::statement('UPDATE discounts SET description = "" WHERE description IS NULL');
            DB::statement('ALTER TABLE discounts MODIFY description LONGTEXT NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('UPDATE discounts SET percentage = 0 WHERE percentage IS NULL');
            DB::statement('ALTER TABLE discounts ALTER COLUMN percentage SET NOT NULL');
            DB::statement("UPDATE discounts SET description = '' WHERE description IS NULL");
            DB::statement('ALTER TABLE discounts ALTER COLUMN description SET NOT NULL');
        }
    }
};
