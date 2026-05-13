<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->enum('level', ['lycee', 'universite', 'tous'])->default('tous');
            $table->integer('order')->default(0);
            $table->integer('duration_hours')->default(2);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(10);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->enum('type', ['pdf', 'image', 'infographie', 'autre'])->default('pdf');
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('modules');
    }
};
