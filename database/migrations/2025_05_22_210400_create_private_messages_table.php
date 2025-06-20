<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('private_messages', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('content');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('recipient_id');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('sender_deleted')->default(false);
            $table->boolean('recipient_deleted')->default(false);
            $table->boolean('is_important')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('archived_by_sender')->default(false);
            $table->boolean('archived_by_recipient')->default(false);
            $table->boolean('request_read_receipt')->default(false);
            $table->unsignedBigInteger('parent_message_id')->nullable();
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_message_id')->references('id')->on('private_messages')->onDelete('set null');

            $table->index('sender_id');
            $table->index('recipient_id');
            $table->index('is_read');
            $table->index('is_important');
            $table->index('is_archived');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_messages');
    }
};
