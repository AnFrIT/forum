<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPriorityColumnToReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            // Check if the column already exists to avoid errors
            if (!Schema::hasColumn('reports', 'priority')) {
                // For PostgreSQL, we need to use raw queries to add enum types
                DB::statement("ALTER TABLE reports ADD COLUMN priority VARCHAR(10) DEFAULT 'normal' NOT NULL");
                
                // Add index separately
                DB::statement("CREATE INDEX reports_priority_index ON reports (priority)");
            }
        } catch (\Exception $e) {
            // Log the error but don't crash migration
            error_log('Error adding priority column: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('reports', 'priority')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropIndex(['priority']);
                $table->dropColumn('priority');
            });
        }
    }
} 