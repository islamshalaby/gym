<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiOptionsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_options_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('multi_option_id')->unsigned();
            $table->foreign('multi_option_id')->references('id')->on('multi_options')->onDelete('restrict');
            $table->integer('category_id');
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
        Schema::dropIfExists('multi_options_categories');
    }
}
