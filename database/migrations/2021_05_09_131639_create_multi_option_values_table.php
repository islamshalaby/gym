<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiOptionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_option_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('multi_option_id')->unsigned();
            $table->foreign('multi_option_id')->references('id')->on('multi_options')->onDelete('restrict');
            $table->string('value_ar');
            $table->string('value_en');
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
        Schema::dropIfExists('multi_option_values');
    }
}
