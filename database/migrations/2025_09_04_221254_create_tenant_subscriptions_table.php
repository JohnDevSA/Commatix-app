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
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // Changed to string for UUID
            $table->string('plan_name');
            $table->enum('billing_interval', ['monthly', 'annually']);
            $table->decimal('amount', 10, 2); // Added precision
            $table->string('currency')->default('ZAR');
            $table->enum('status', ['active', 'cancelled', 'past_due', 'unpaid', 'trialing']);
            $table->timestamp('current_period_start')->nullable(); // Made nullable
            $table->timestamp('current_period_end')->nullable(); // Made nullable
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->string('stripe_subscription_id')->nullable();
            $table->string('payfast_subscription_id')->nullable();
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};
