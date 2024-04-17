<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->double('price', 8, 2);
            $table->enum('status', ['new', 'progress', 'delivered', 'cancel'])->default('new');
            $table->integer('quantity');
            $table->double('amount', 8, 2);
            $table->timestamps();

            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('order_id')->nullable()->references('id')->on('orders')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
