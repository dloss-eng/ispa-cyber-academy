<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['basic','premium','enterprise'])->default('basic');
            $table->integer('amount');
            $table->enum('payment_method', ['mtn_momo','orange_money','wave','visa','mastercard'])->nullable();
            $table->string('transaction_ref')->nullable();
            $table->enum('status', ['pending','active','expired','cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subscriptions'); }
};
