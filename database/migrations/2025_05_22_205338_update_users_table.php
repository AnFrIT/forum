<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('avatar')->nullable()->after('email');
            $table->text('signature')->nullable();
            $table->integer('posts_count')->default(0);
            $table->integer('topics_count')->default(0);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_until')->nullable();
            $table->string('ban_reason')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->string('locale')->default('ru');

            $table->index('username');
            $table->index('is_banned');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'avatar', 'signature', 'posts_count',
                'topics_count', 'is_banned', 'banned_until',
                'ban_reason', 'last_activity', 'locale',
            ]);
        });
    }
};
