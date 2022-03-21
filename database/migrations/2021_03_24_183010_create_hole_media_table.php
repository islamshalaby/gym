<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoleMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hole_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image');
            $table->enum('type',['image','video'])->default('image');
            $table->bigInteger('hole_id')->unsigned();
            $table->foreign('hole_id')->references('id')->on('holes')->onDelete('restrict');
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
        Schema::dropIfExists('hole_media');
    }
}
