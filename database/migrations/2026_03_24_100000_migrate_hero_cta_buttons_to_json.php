<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_settings', function (Blueprint $table) {
            $table->json('cta_buttons')->nullable()->after('buttons_direction');
        });

        $rows = DB::table('hero_settings')->get();
        foreach ($rows as $row) {
            $cta = [
                [
                    'label' => $row->primary_button_label ?? 'Узнать стоимость',
                    'icon' => $row->primary_button_icon ?? 'fas fa-graduation-cap',
                    'href' => $row->primary_button_href ?? '/prices',
                    'variant' => 'light',
                ],
                [
                    'label' => $row->secondary_button_label ?? 'Записаться',
                    'icon' => $row->secondary_button_icon ?? 'fas fa-phone',
                    'href' => $row->secondary_button_href ?? '#pricing-preview',
                    'variant' => 'outline-light',
                ],
            ];
            DB::table('hero_settings')->where('id', $row->id)->update([
                'cta_buttons' => json_encode($cta, JSON_UNESCAPED_UNICODE),
            ]);
        }

        Schema::table('hero_settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_button_label',
                'primary_button_icon',
                'primary_button_href',
                'secondary_button_label',
                'secondary_button_icon',
                'secondary_button_href',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('hero_settings', function (Blueprint $table) {
            $table->string('primary_button_label')->default('Узнать стоимость');
            $table->string('primary_button_icon')->default('fas fa-graduation-cap');
            $table->string('primary_button_href')->default('/prices');
            $table->string('secondary_button_label')->default('Записаться');
            $table->string('secondary_button_icon')->default('fas fa-phone');
            $table->string('secondary_button_href')->default('#pricing-preview');
        });

        $rows = DB::table('hero_settings')->get();
        foreach ($rows as $row) {
            $cta = json_decode($row->cta_buttons ?? '[]', true);
            $first = is_array($cta) && isset($cta[0]) ? $cta[0] : [];
            $second = is_array($cta) && isset($cta[1]) ? $cta[1] : [];
            DB::table('hero_settings')->where('id', $row->id)->update([
                'primary_button_label' => $first['label'] ?? 'Узнать стоимость',
                'primary_button_icon' => $first['icon'] ?? 'fas fa-graduation-cap',
                'primary_button_href' => $first['href'] ?? '/prices',
                'secondary_button_label' => $second['label'] ?? 'Записаться',
                'secondary_button_icon' => $second['icon'] ?? 'fas fa-phone',
                'secondary_button_href' => $second['href'] ?? '#pricing-preview',
            ]);
        }

        Schema::table('hero_settings', function (Blueprint $table) {
            $table->dropColumn('cta_buttons');
        });
    }
};
