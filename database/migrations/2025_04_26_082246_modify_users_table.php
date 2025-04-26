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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'username', 'country_code', 'last_login_at']);
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->string('name')->after('role_id');
            $table->text('address')->nullable()->after('name');
            $table->renameColumn('mobile_number', 'mobile');
            $table->string('status')->default('inactive')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert column drops
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('country_code')->nullable();
            $table->timestamp('last_login_at')->nullable();

            // Remove added columns
            $table->dropColumn(['role_id', 'name', 'address']);
            $table->renameColumn('mobile', 'mobile_number');
            $table->string('status')->default('active')->change();
        });
    }
};