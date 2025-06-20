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
        Schema::table('users', function (Blueprint $table) {
            // Проверяем и добавляем только те колонки, которых нет

            if (! Schema::hasColumn('users', 'preferred_language')) {
                $table->string('preferred_language', 5)->default('ru')->after('email');
            }

            if (! Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('email_verified_at');
            }

            if (! Schema::hasColumn('users', 'is_moderator')) {
                $table->boolean('is_moderator')->default(false)->after('is_admin');
            }

            if (! Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('is_moderator');
            }

            if (! Schema::hasColumn('users', 'banned_at')) {
                $table->timestamp('banned_at')->nullable()->after('is_banned');
            }

            if (! Schema::hasColumn('users', 'ban_reason')) {
                $table->string('ban_reason')->nullable()->after('banned_at');
            }

            if (! Schema::hasColumn('users', 'ban_expires_at')) {
                $table->timestamp('ban_expires_at')->nullable()->after('ban_reason');
            }

            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('ban_expires_at');
            }

            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('bio');
            }

            if (! Schema::hasColumn('users', 'location')) {
                $table->string('location')->nullable()->after('avatar');
            }

            if (! Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('location');
            }

            if (! Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('website');
            }

            if (! Schema::hasColumn('users', 'last_ip')) {
                $table->ipAddress('last_ip')->nullable()->after('last_activity_at');
            }

            if (! Schema::hasColumn('users', 'posts_count')) {
                $table->unsignedInteger('posts_count')->default(0)->after('last_ip');
            }

            if (! Schema::hasColumn('users', 'topics_count')) {
                $table->unsignedInteger('topics_count')->default(0)->after('posts_count');
            }

            if (! Schema::hasColumn('users', 'reputation')) {
                $table->unsignedInteger('reputation')->default(0)->after('topics_count');
            }

            // УДАЛИЛ username - он уже есть в предыдущей миграции!
            // НО исправим тип поля username, чтобы он был nullable
            if (Schema::hasColumn('users', 'username')) {
                // Сделаем username nullable без doctrine/dbal
                DB::statement('ALTER TABLE users ALTER COLUMN username DROP NOT NULL');
            }
        });

        // Добавляем индексы если их нет
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['is_admin', 'is_moderator'], 'users_admin_moderator_index');
                $table->index(['is_banned', 'banned_at'], 'users_banned_index');
                $table->index('last_activity_at', 'users_last_activity_index');
                $table->index('preferred_language', 'users_language_index');
            });
        } catch (\Exception $e) {
            // Индексы уже существуют, игнорируем
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем индексы
            try {
                $table->dropIndex('users_admin_moderator_index');
                $table->dropIndex('users_banned_index');
                $table->dropIndex('users_last_activity_index');
                $table->dropIndex('users_language_index');
            } catch (\Exception $e) {
                // Индексы не существуют, игнорируем
            }

            // Удаляем колонки если они существуют (БЕЗ username!)
            $columnsToRemove = [
                'preferred_language', 'is_admin', 'is_moderator', 'is_banned',
                'banned_at', 'ban_reason', 'ban_expires_at', 'bio', 'avatar',
                'location', 'website', 'last_activity_at', 'last_ip',
                'posts_count', 'topics_count', 'reputation',
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
