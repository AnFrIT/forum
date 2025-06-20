<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:schema {table?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the schema of a database table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tableName = $this->argument('table') ?? 'private_messages';
        
        if (!Schema::hasTable($tableName)) {
            $this->error("Table {$tableName} does not exist");
            return 1;
        }
        
        $this->info("Table {$tableName} exists");
        
        // Get column information
        $columns = DB::select("SELECT column_name, data_type, character_maximum_length, column_default, is_nullable
                            FROM information_schema.columns 
                            WHERE table_name = ?
                            ORDER BY ordinal_position", [$tableName]);
        
        $this->info("Columns in {$tableName} table:");
        foreach ($columns as $column) {
            $this->line("- {$column->column_name} ({$column->data_type})" . 
                ($column->is_nullable === 'YES' ? ' NULL' : ' NOT NULL') . 
                (isset($column->column_default) ? " DEFAULT {$column->column_default}" : ""));
        }
        
        return 0;
    }
} 