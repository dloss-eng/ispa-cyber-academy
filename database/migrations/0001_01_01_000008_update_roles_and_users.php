<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add avatar upload + 2FA fields to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('is_active');
            $table->string('two_factor_code', 6)->nullable()->after('two_factor_enabled');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
        });

        // Add logo field to etablissements
        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_enabled', 'two_factor_code', 'two_factor_expires_at']);
        });
        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
