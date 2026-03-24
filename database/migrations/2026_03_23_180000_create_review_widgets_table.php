<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('provider', 32);
            $table->string('render_type', 32);
            $table->json('config')->nullable();
            $table->boolean('show_on_home')->default(false);
            $table->unsignedInteger('home_sort_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['show_on_home', 'home_sort_order']);
            $table->index(['provider', 'render_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_widgets');
    }
};
