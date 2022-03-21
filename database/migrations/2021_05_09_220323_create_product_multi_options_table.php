<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMultiOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_multi_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->bigInteger('multi_option_id')->unsigned();
            $table->foreign('multi_option_id')->references('id')->on('multi_options')->onDelete('restrict');
            $table->bigInteger('multi_option_value_id')->unsigned();
            $table->foreign('multi_option_value_id')->references('id')->on('multi_option_values')->onDelete('restrict');
            $table->string('final_price');
            $table->string('price_before_offer')->nullable()->default('0');
            $table->integer('total_quatity');
            $table->integer('remaining_quantity');
            $table->string('barcode')->nullable();
            $table->string('stored_number')->nullable();
            $table->integer('sold_count')->default(0);
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
        Schema::dropIfExists('product_multi_options');
    }
}
