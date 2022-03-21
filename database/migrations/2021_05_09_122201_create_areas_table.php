<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('delivery_cost');
            $table->string('place_id');
            $table->string('formatted_address_ar');
            $table->string('formatted_address_en');
            $table->bigInteger('governorate_id')->unsigned();
            $table->foreign('governorate_id')->references('id')->on('governorates')->onDelete('restrict');
            $table->tinyInteger('deleted')->default(0);;
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
        Schema::dropIfExists('areas');
    }
}
