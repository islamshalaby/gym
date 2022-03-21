<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->bigInteger('address_id')->unsigned();
            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('restrict');
            $table->integer('payment_method');
            $table->string('subtotal_price');
            $table->string('delivery_cost');
            $table->string('total_price');
            $table->integer('status');
            $table->string('order_number');
            $table->bigInteger('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('shops')->onDelete('restrict');
            $table->integer('main_id');
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
}
