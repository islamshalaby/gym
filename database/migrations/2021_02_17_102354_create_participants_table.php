<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->enum('user_type',['coach','user'])->default('user');

            $table->bigInteger('other_user_id')->unsigned();
            $table->foreign('other_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->enum('other_user_type',['coach','user'])->default('user');

            $table->bigInteger('conversation_id')->unsigned();
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('restrict');

            $table->bigInteger('coach_id')->unsigned();
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('restrict');
            $table->primary(['user_id', 'conversation_id']);

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
        Schema::dropIfExists('participants');
    }
}
