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
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('fullname');
            }
            if (!Schema::hasColumn('users', 'company_address')) {
                $table->text('company_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'other_address')) {
                $table->text('other_address')->nullable()->after('company_address');
            }
            if (!Schema::hasColumn('users', 'default_address_type')) {
                $table->string('default_address_type')->default('home')->after('other_address');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'company_address',
                'other_address',
                'default_address_type',
                'two_factor_enabled',
            ]);
        });
    }
};
