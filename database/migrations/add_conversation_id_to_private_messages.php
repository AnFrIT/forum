<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConversationIdToPrivateMessages extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('user_one_deleted')->default(false);
            $table->boolean('user_two_deleted')->default(false);
            $table->timestamps();
            
            // Ensure unique conversations between users
            $table->unique(['user_one_id', 'user_two_id']);
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->boolean('archived_by_sender')->default(false)->after('is_read');
            $table->boolean('archived_by_recipient')->default(false)->after('archived_by_sender');
            $table->boolean('is_archived')->default(false)->after('archived_by_recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
            $table->dropColumn('archived_by_sender');
            $table->dropColumn('archived_by_recipient');
            $table->dropColumn('is_archived');
        });

        Schema::dropIfExists('conversations');
    }
}; 