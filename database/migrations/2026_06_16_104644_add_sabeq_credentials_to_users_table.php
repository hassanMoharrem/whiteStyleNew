<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sabeq_login_token')->nullable()->after('password');
            $table->string('sabeq_profile_id')->nullable()->after('sabeq_login_token');
            $table->string('sabeq_api_key')->nullable()->after('sabeq_profile_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sabeq_login_token', 'sabeq_profile_id', 'sabeq_api_key']);
        });
    }
};
