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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('total_received', 10, 2)->after('total_amount')->default(0);
            $table->decimal('change_due', 10, 2)->after('total_received')->default(0);
            $table->dropColumn('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_method')->after('discount');
            $table->dropColumn(['total_received', 'change_due']);
        });
    }
};