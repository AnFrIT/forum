<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // First copy title data to signature if signature is empty
            DB::statement('UPDATE users SET signature = title WHERE signature IS NULL');
            
            // Then drop the title column
            $table->dropColumn('title');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the title column
            $table->string('title')->nullable();
            
            // Copy signature data back to title
            DB::statement('UPDATE users SET title = signature');
        });
    }
}; 