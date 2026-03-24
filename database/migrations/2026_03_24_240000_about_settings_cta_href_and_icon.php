<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->string('cta_icon')->default('fas fa-graduation-cap')->after('cta_button_text');
            $table->string('cta_href', 500)->default('/prices')->after('cta_icon');
        });

        $allowed = \App\Support\HeroCtaLinkCatalog::allowedPaths();
        $fallback = '/prices';

        $rows = DB::table('about_settings')->get();
        foreach ($rows as $row) {
            $name = $row->cta_route_name ?? 'prices';
            $map = [
                'prices' => '/prices',
                'contacts' => '/contacts',
                'about' => '/about',
                'blog' => '/blog',
                'discounts' => '/discounts',
                'home' => '/',
            ];
            $href = $map[$name] ?? null;
            if ($href === null) {
                $href = str_starts_with((string) $name, '/') ? $name : '/' . ltrim((string) $name, '/');
            }
            if (!in_array($href, $allowed, true)) {
                $href = $fallback;
            }

            DB::table('about_settings')->where('id', $row->id)->update([
                'cta_href' => $href,
                'cta_icon' => 'fas fa-graduation-cap',
            ]);
        }

        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn('cta_route_name');
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->string('cta_route_name')->default('prices');
        });

        $map = [
            '/prices' => 'prices',
            '/contacts' => 'contacts',
            '/about' => 'about',
            '/blog' => 'blog',
            '/discounts' => 'discounts',
            '/' => 'home',
        ];

        foreach (DB::table('about_settings')->get() as $row) {
            $href = $row->cta_href ?? '/prices';
            $name = $map[$href] ?? 'prices';
            DB::table('about_settings')->where('id', $row->id)->update([
                'cta_route_name' => $name,
            ]);
        }

        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn(['cta_icon', 'cta_href']);
        });
    }
};
