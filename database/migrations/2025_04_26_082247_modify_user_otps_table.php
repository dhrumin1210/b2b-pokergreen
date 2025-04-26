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
        Schema::table('user_otps', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('otp_for');

            // Add the new string column
            $table->string('otp_for')->after('otp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_otps', function (Blueprint $table) {
            // Drop the string column
            $table->dropColumn('otp_for');

            // Re-add the enum column
            $table->enum('otp_for', ['forgot_password', 'reset_password'])->after('otp');
        });
    }
};
