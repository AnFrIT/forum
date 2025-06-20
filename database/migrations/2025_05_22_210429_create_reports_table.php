<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable'); // Этот метод уже создает индекс автоматически
            $table->unsignedBigInteger('reporter_id');
            $table->string('reason');
            $table->text('description');
            $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending');
            $table->enum('priority', ['normal', 'high'])->default('normal');
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
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
