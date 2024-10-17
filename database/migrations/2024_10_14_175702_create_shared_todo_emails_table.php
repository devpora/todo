<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_todo_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shared_todo_id');
            $table->string('email');
            $table->timestamps();

            $table->foreign('shared_todo_id')->references('id')->on('shared_todos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_todos');
    }
};
