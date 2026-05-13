<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('module_id')
                  ->nullable()
                  ->after('lesson_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Rendre lesson_id nullable (quiz peut appartenir à un module OU une leçon)
            $table->foreignId('lesson_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
            $table->foreignId('lesson_id')->nullable(false)->change();
        });
    }
};