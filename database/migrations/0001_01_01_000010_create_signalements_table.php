<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number')->unique();
            $table->enum('type', ['sms_frauduleux', 'phishing_whatsapp', 'phishing_email', 'faux_site', 'arnaque_mobile_money', 'cyberharcèlement', 'autre']);
            $table->text('description');
            $table->string('suspect_contact')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->date('incident_date')->nullable();
            $table->enum('status', ['nouveau', 'en_cours', 'traite', 'rejete'])->default('nouveau');
            $table->string('ai_category')->nullable();
            $table->integer('ai_confidence')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};
