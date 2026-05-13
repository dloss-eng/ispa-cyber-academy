<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('enseignant_id')->nullable()->after('etablissement_id')->constrained('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('classes', function (Blueprint $table) { $table->dropColumn('enseignant_id'); });
    }
};
