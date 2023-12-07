<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('requested_user_id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('filled_user_id')->nullable();
            $table->string('type');
            $table->dateTime('filled_time')->nullable();
            $table->timestamps();
        });


        Schema::table('queues', function (Blueprint $table) {
            $table->foreign('requested_user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('lead_id')
                ->references('id')->on('leads')
                ->onDelete('cascade');
            $table->foreign('filled_user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue');
    }
}
