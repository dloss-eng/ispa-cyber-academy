<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Challenges CTF ─────────────────────────────────────────
        Schema::create('ctf_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');           // Intro courte (visible en liste)
            $table->longText('scenario');           // Contenu HTML complet du challenge
            $table->enum('type', ['textual_analysis', 'flag_hunt']);
            $table->string('flag');                 // Réponse attendue (insensible à la casse)
            $table->json('hints')->nullable();      // Tableau d'indices [{text, cost_points}]
            $table->integer('points')->default(100);
            $table->enum('difficulty', ['facile', 'moyen', 'difficile'])->default('facile');
            $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_published')->default(false);
            $table->integer('order')->default(0);
            $table->integer('max_attempts')->default(0); // 0 = illimité
            $table->timestamps();
        });

        // ── Tentatives CTF ─────────────────────────────────────────
        Schema::create('ctf_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')
                  ->constrained('ctf_challenges')->cascadeOnDelete();
            $table->string('submitted_flag');
            $table->boolean('is_correct')->default(false);
            $table->integer('hints_used')->default(0);   // Nombre d'indices révélés
            $table->integer('points_earned')->default(0);
            $table->timestamp('solved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'challenge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctf_attempts');
        Schema::dropIfExists('ctf_challenges');
    }
};
