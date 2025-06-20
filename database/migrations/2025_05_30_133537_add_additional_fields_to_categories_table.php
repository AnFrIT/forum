<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Добавляем дополнительные поля, если их нет
            if (!Schema::hasColumn('categories', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('categories', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('slug');
            }
            
            if (!Schema::hasColumn('categories', 'color')) {
                $table->string('color')->default('blue')->after('icon');
            }
            
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('color');
            }
            
            if (!Schema::hasColumn('categories', 'is_private')) {
                $table->boolean('is_private')->default(false)->after('is_active');
            }
            
            if (!Schema::hasColumn('categories', 'is_readonly')) {
                $table->boolean('is_readonly')->default(false)->after('is_private');
            }
            
            if (!Schema::hasColumn('categories', 'requires_approval')) {
                $table->boolean('requires_approval')->default(false)->after('is_readonly');
            }
            
            if (!Schema::hasColumn('categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('requires_approval');
            }
            
            if (!Schema::hasColumn('categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $columns = [
                'name_ar', 'description_ar', 'icon', 'color', 'image',
                'is_private', 'is_readonly', 'requires_approval',
                'meta_title', 'meta_description'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};