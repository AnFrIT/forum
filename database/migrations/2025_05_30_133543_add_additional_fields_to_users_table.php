<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Добавляем поля, если их нет
            if (!Schema::hasColumn('users', 'preferred_language')) {
                $table->string('preferred_language', 5)->default('ru')->after('password');
            }
            
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('preferred_language');
            }
            
            if (!Schema::hasColumn('users', 'is_moderator')) {
                $table->boolean('is_moderator')->default(false)->after('is_admin');
            }
            
            if (!Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('is_moderator');
            }
            
            if (!Schema::hasColumn('users', 'banned_at')) {
                $table->timestamp('banned_at')->nullable()->after('is_banned');
            }
            
            if (!Schema::hasColumn('users', 'ban_reason')) {
                $table->text('ban_reason')->nullable()->after('banned_at');
            }
            
            if (!Schema::hasColumn('users', 'ban_expires_at')) {
                $table->timestamp('ban_expires_at')->nullable()->after('ban_reason');
            }
            
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('ban_expires_at');
            }
            
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location')->nullable()->after('avatar');
            }
            
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('website');
            }
            
            if (!Schema::hasColumn('users', 'last_ip')) {
                $table->ipAddress('last_ip')->nullable()->after('last_activity_at');
            }
            
            if (!Schema::hasColumn('users', 'posts_count')) {
                $table->integer('posts_count')->default(0)->after('last_ip');
            }
            
            if (!Schema::hasColumn('users', 'topics_count')) {
                $table->integer('topics_count')->default(0)->after('posts_count');
            }
            
            if (!Schema::hasColumn('users', 'reputation')) {
                $table->integer('reputation')->default(0)->after('topics_count');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'preferred_language', 'is_admin', 'is_moderator', 'is_banned',
                'banned_at', 'ban_reason', 'ban_expires_at', 'bio', 'avatar',
                'location', 'website', 'last_activity_at', 'last_ip',
                'posts_count', 'topics_count', 'reputation'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};