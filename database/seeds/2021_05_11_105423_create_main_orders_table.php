<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->bigInteger('address_id')->unsigned();
            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('restrict');
            $table->integer('payment_method');
            $table->string('subtotal_price')->nullable();
            $table->string('delivery_cost')->nullable();
            $table->string('total_price')->nullable();
            $table->integer('status')->default(1);
            $table->string('main_order_number');
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
        Schema::dropIfExists('main_orders');
    }
}
