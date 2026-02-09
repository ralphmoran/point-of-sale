<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('order_type', 20)->default('dine_in')->after('payment_method');
            $table->string('customer_name')->nullable()->after('order_type');
            $table->string('table_number', 10)->nullable()->after('customer_name');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'customer_name', 'table_number']);
        });
    }
};
