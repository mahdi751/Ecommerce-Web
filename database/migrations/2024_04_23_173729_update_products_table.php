<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove the current_highest_bid column
            $table->dropColumn('current_highest_bid');

            // Add a new column for the bid_id foreign key
            $table->unsignedBigInteger('highest_bid_id')->nullable();
            $table->foreign('highest_bid_id')->references('id')->on('bids')->onDelete('set null');
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
            // Drop the foreign key constraint and the bid_id column
            $table->dropForeign(['highest_bid_id']);
            $table->dropColumn('highest_bid_id');

            // Add back the current_highest_bid column
            $table->decimal('current_highest_bid', 8, 2)->nullable();
        });
    }
}
