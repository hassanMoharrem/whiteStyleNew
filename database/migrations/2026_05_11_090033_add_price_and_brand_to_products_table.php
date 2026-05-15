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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('description');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->integer('review_count')->default(5)->after('discount_price');
            $table->foreignId('brand_id')->nullable()->after('review_count')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['price', 'discount_price', 'review_count', 'brand_id']);
        });
    }
};
