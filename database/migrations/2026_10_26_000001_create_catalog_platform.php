<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('public_id', 26)->unique();
            $t->string('slug', 60)->unique();
            $t->string('name', 100);
            $t->string('description', 300)->nullable();
            $t->string('whatsapp', 20)->nullable();
            $t->string('instagram', 100)->nullable();
            $t->string('facebook', 200)->nullable();
            $t->string('status', 20)->default('draft');
            $t->string('plan', 20)->default('free');
            $t->unsignedInteger('catalog_version')->default(1);
            $t->unsignedInteger('order_sequence')->default(0);
            $t->unsignedBigInteger('storage_bytes')->default(0);
            $t->json('appearance');
            $t->json('appearance_draft');
            $t->boolean('email_orders')->default(true);
            $t->timestamps();
        });
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $t->string('public_id', 26)->unique();
            $t->string('name', 80);
            $t->string('slug', 100);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique(['shop_id', 'id']);
            $t->unique(['shop_id', 'name']);
        });
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('category_id')->nullable();
            $t->string('public_id', 26)->unique();
            $t->string('name', 120);
            $t->string('slug', 150);
            $t->text('description')->nullable();
            $t->unsignedBigInteger('price_minor');
            $t->unsignedBigInteger('previous_price_minor')->nullable();
            $t->string('sku', 60)->nullable();
            $t->boolean('available')->default(true);
            $t->string('status', 20)->default('active');
            $t->unsignedInteger('sort_order')->default(0);
            $t->unsignedInteger('version')->default(1);
            $t->timestamps();
            $t->unique(['shop_id', 'id']);
            $t->unique(['shop_id', 'sku']);
            $t->index(['shop_id', 'status', 'sort_order', 'id']);
            $t->foreign(['shop_id', 'category_id'])->references(['shop_id', 'id'])->on('categories');
        });
        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('product_id')->nullable();
            $t->string('kind', 20);
            $t->string('path');
            $t->json('variants');
            $t->unsignedBigInteger('bytes');
            $t->unsignedInteger('width');
            $t->unsignedInteger('height');
            $t->timestamps();
            $t->foreign(['shop_id', 'product_id'])->references(['shop_id', 'id'])->on('products');
        });
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $t->string('public_id', 26)->unique();
            $t->unsignedInteger('number');
            $t->string('status', 20)->default('pending');
            $t->string('buyer_name', 100);
            $t->string('buyer_phone', 20);
            $t->string('note', 500)->nullable();
            $t->unsignedBigInteger('total_minor');
            $t->string('currency', 3)->default('ARS');
            $t->string('session_hash', 64);
            $t->uuid('idempotency_key');
            $t->string('payload_hash', 64);
            $t->timestamps();
            $t->unique(['shop_id', 'id']);
            $t->unique(['shop_id', 'number']);
            $t->unique(['shop_id', 'session_hash', 'idempotency_key'], 'order_idempotency_unique');
            $t->index(['shop_id', 'status', 'created_at']);
        });
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('shop_id');
            $t->unsignedBigInteger('order_id');
            $t->unsignedBigInteger('product_id');
            $t->string('name', 120);
            $t->string('sku', 60)->nullable();
            $t->unsignedBigInteger('unit_price_minor');
            $t->unsignedInteger('quantity');
            $t->unsignedBigInteger('total_minor');
            $t->foreign(['shop_id', 'order_id'])->references(['shop_id', 'id'])->on('orders')->cascadeOnDelete();
            $t->foreign(['shop_id', 'product_id'])->references(['shop_id', 'id'])->on('products');
        });
        Schema::create('order_events', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('shop_id');
            $t->unsignedBigInteger('order_id');
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('from_status', 20)->nullable();
            $t->string('to_status', 20);
            $t->timestamp('created_at');
            $t->foreign(['shop_id', 'order_id'])->references(['shop_id', 'id'])->on('orders')->cascadeOnDelete();
        });
        Schema::create('outbox', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('order_id')->nullable();
            $t->string('recipient');
            $t->string('status', 20)->default('pending');
            $t->unsignedInteger('attempts')->default(0);
            $t->timestamp('next_attempt_at');
            $t->timestamp('lease_until')->nullable();
            $t->uuid('lease_token')->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->string('error_code', 80)->nullable();
            $t->timestamps();
            $t->unique('order_id');
            $t->index(['status', 'next_attempt_at']);
            $t->foreign(['shop_id', 'order_id'])->references(['shop_id', 'id'])->on('orders')->cascadeOnDelete();
        });
        Schema::create('daily_metrics', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $t->date('day');
            $t->unsignedInteger('views')->default(0);
            $t->unsignedInteger('whatsapp_clicks')->default(0);
            $t->unique(['shop_id', 'day']);
        });
        Schema::create('local_messages', function (Blueprint $t) {
            $t->id();
            $t->string('recipient');
            $t->string('subject');
            $t->longText('html');
            $t->timestamp('created_at');
        });
    }

    public function down(): void
    {
        foreach (['local_messages', 'daily_metrics', 'outbox', 'order_events', 'order_items', 'orders', 'media', 'products', 'categories', 'shops'] as $name) {
            Schema::dropIfExists($name);
        }
    }
};
