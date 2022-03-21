<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCaochAsksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_caoch_asks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ask_num_free')->unsigned()->default(1);
            $table->bigInteger('ask_num_payed')->unsigned()->default(0);
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->bigInteger('caoch_id')->unsigned();
            $table->foreign('caoch_id')->references('id')->on('coaches')->onDelete('restrict');
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
        Schema::dropIfExists('user_caoch_asks');
    }
}
