<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('password');
            $table->enum('subscription_status', ['active', 'canceled', 'trialing', 'free'])->default('free')->after('stripe_customer_id');
            $table->enum('subscription_tier', ['free', 'pro'])->default('free')->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'subscription_status', 'subscription_tier']);
        });
    }
};
