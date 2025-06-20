<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('views_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->timestamp('last_post_at')->nullable();
            $table->unsignedBigInteger('last_post_user_id')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_post_user_id')->references('id')->on('users')->onDelete('set null');

            $table->index('slug');
            $table->index('category_id');
            $table->index('user_id');
            $table->index('is_pinned');
            $table->index('created_at');
            $table->index('last_post_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('topics');
    }
};
