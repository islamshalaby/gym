<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoachTimeWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_time_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('time_from');
            $table->time('time_to');
            $table->bigInteger('coach_id')->unsigned();
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('restrict');
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
        Schema::dropIfExists('coach_time_works');
    }
}
