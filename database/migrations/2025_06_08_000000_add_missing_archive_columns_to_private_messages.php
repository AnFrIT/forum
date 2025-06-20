<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL query for PostgreSQL to add columns if they don't exist
        $columnChecks = [
            "SELECT column_name FROM information_schema.columns WHERE table_name = 'private_messages' AND column_name = 'archived_by_sender'",
            "SELECT column_name FROM information_schema.columns WHERE table_name = 'private_messages' AND column_name = 'archived_by_recipient'",
            "SELECT column_name FROM information_schema.columns WHERE table_name = 'private_messages' AND column_name = 'is_archived'"
        ];
        
        $columnAdds = [
            "ALTER TABLE private_messages ADD COLUMN IF NOT EXISTS archived_by_sender BOOLEAN DEFAULT FALSE",
            "ALTER TABLE private_messages ADD COLUMN IF NOT EXISTS archived_by_recipient BOOLEAN DEFAULT FALSE",
            "ALTER TABLE private_messages ADD COLUMN IF NOT EXISTS is_archived BOOLEAN DEFAULT FALSE"
        ];
        
        foreach ($columnAdds as $query) {
            DB::statement($query);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop columns if they exist - but we won't actually do this
        // as it could cause data loss in production
    }
}; 