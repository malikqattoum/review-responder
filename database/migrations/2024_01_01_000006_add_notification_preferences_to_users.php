<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_new_reviews')->default(true)->after('subscription_tier');
            $table->boolean('notify_negative_reviews')->default(true)->after('notify_new_reviews');
            $table->string('notification_email')->nullable()->after('notify_negative_reviews');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_new_reviews', 'notify_negative_reviews', 'notification_email']);
        });
    }
};
