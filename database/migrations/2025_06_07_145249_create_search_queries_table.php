<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем, существует ли таблица search_queries
        if (!Schema::hasTable('search_queries')) {
            Schema::create('search_queries', function (Blueprint $table) {
                $table->id();
                $table->string('term')->index();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->integer('count')->default(1);
                $table->json('filters')->nullable();
                $table->timestamp('last_searched_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Не удаляем таблицу, так как она могла быть создана другой миграцией
        // Schema::dropIfExists('search_queries');
    }
};
