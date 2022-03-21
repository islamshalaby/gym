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
            $table->string('video')->nullable();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('barcode')->nullable();
            $table->tinyInteger('offer')->default(0);
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->string('final_price')->nullable();
            $table->string('price_before_offer')->nullable();
            $table->double('offer_percentage');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->bigInteger('sub_category_id')->unsigned()->nullable();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('restrict');
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('restrict');
            $table->tinyInteger('deleted')->default(0);
            $table->tinyInteger('hidden')->default(0);
            $table->integer('total_quatity')->nullable();
            $table->integer('remaining_quantity')->nullable();
            $table->integer('stored_number')->nullable();
            $table->integer('sold_count');
            $table->integer('refund_count');
            $table->integer('multi_options');
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('shops')->onDelete('restrict');
            $table->integer('order_period')->nullable();
            $table->integer('type')->nullable();
            $table->tinyInteger('reviewed')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_media');
    }
}
