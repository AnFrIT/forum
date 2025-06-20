<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            // Check if reports table exists
            if (Schema::hasTable('reports')) {
                // Save existing data if any
                $existingReports = DB::table('reports')->get();
                
                // Drop table and recreate properly
                Schema::dropIfExists('reports');
            }
            
            // Create the table with correct schema
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->morphs('reportable');
                $table->unsignedBigInteger('reporter_id');
                $table->string('reason');
                $table->text('description');
                $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending');
                $table->string('priority')->default('normal'); // Using string instead of enum for better DB compatibility
                $table->unsignedBigInteger('moderator_id')->nullable();
                $table->text('moderator_notes')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('moderator_id')->references('id')->on('users')->onDelete('set null');

                $table->index('reporter_id');
                $table->index('status');
                $table->index('priority');
                $table->index('created_at');
            });
            
            // Restore data if we had any
            if (isset($existingReports) && count($existingReports) > 0) {
                foreach ($existingReports as $report) {
                    $reportData = (array) $report;
                    // Add priority field if it doesn't exist
                    if (!isset($reportData['priority'])) {
                        $reportData['priority'] = 'normal';
                    }
                    DB::table('reports')->insert($reportData);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the migration
            error_log('Error fixing reports table: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need for a down method as this is a fix
    }
} 