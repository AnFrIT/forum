<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPrivateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_messages', function (Blueprint $table) {
            // Add missing columns
            $table->boolean('is_archived')->default(false);
            $table->boolean('archived_by_sender')->default(false);
            $table->boolean('archived_by_recipient')->default(false);
            $table->boolean('is_important')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Make existing columns nullable if they don't exist
            if (!Schema::hasColumn('private_messages', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->foreign('parent_id')->references('id')->on('private_messages')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropColumn([
                'is_archived',
                'archived_by_sender',
                'archived_by_recipient',
                'is_important',
                'read_at'
            ]);
            
            // Careful when dropping parent_id as it might be used by other code
            if (Schema::hasColumn('private_messages', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
} 