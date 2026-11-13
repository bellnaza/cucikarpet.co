<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('vehicle')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('password');
            $table->string('phone', 30)->nullable()->after('email');
            $table->foreignId('team_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        Schema::create('carpet_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->decimal('rate_per_sqft', 8, 2);
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30)->index();
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('area', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // One row per month (period = YYMM). Locked while the next number is taken.
        Schema::create('order_sequences', function (Blueprint $table) {
            $table->string('period', 4)->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('baru')->index();
            $table->string('payment_status', 20)->default('unpaid')->index();
            $table->string('payment_method', 20)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->date('pickup_date')->nullable()->index();
            $table->string('pickup_slot', 30)->nullable();
            $table->unsignedSmallInteger('pickup_seq')->nullable();
            $table->date('delivery_date')->nullable()->index();
            $table->unsignedSmallInteger('delivery_seq')->nullable();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_sqft', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('pickup_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('source', 20)->default('admin');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('tag', 30)->unique();
            $table->foreignId('carpet_type_id')->constrained()->restrictOnDelete();
            $table->decimal('width_ft', 6, 2);
            $table->decimal('length_ft', 6, 2);
            $table->decimal('sqft', 10, 2);
            $table->decimal('rate', 8, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('order_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('kind', 20)->default('sebelum');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20);
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('to');
            $table->string('template', 20);
            $table->string('subject');
            $table->string('status', 20)->default('queued')->index();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_photos');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_sequences');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('carpet_types');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
            $table->dropColumn(['role', 'phone']);
        });
        Schema::dropIfExists('teams');
    }
};
