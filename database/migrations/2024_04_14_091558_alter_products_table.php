<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_event_item')->default(0)->nullable();
            $table->decimal('starting_bid_price', 8, 2)->nullable();
            $table->decimal('minimum_bid_increment', 8, 2)->nullable();
            $table->decimal('current_highest_bid', 8, 2)->nullable();
            $table->decimal('closing_bid', 8, 2)->nullable();
            $table->enum('bid_status', ['open', 'closed'])->default('open')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn(['is_event_item', 'starting_bid_price', 'minimum_bid_increment', 'current_highest_bid', 'closing_bid', 'bid_status', 'event_id']);
        });
    }
}
