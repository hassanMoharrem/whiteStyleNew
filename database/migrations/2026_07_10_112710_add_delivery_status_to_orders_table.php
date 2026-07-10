<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_status', 30)->nullable()->after('status');
            $table->timestamp('delivery_status_updated_at')->nullable()->after('delivery_status');
            $table->index('track_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['track_number']);
            $table->dropColumn(['delivery_status', 'delivery_status_updated_at']);
        });
    }
};