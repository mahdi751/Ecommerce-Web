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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number'); 
            $table->double('sub_total', 8, 2);
            $table->double('coupon', 8, 2)->nullable();
            $table->double('total_amount', 12, 2);
            $table->enum('payment_method', ['cod', 'paypal', 'crypto'])->default('cod');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('status', ['new', 'process', 'delivered', 'cancel'])->default('new');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('country');
            $table->string('currency')->nullable();
            $table->string('post_code')->nullable();
            $table->text('address1');
            $table->text('address2')->nullable();

            $table->foreignId('shipping_id')->references('id')->on('shippings')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
