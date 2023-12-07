<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->datetime('start_time');
            $table->datetime('finish_time');
            $table->string('subject');
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appointment_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appointment_opportunities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('opportunity')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('appointment_availability', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->boolean('approved')->default(false);
            $table->boolean('reviewed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('appointment_availability', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('appointment_types')->onDelete('cascade');
        });

        Schema::table('appointment_opportunities', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('appointment_types')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('appointment_availability');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('appointment_opportunities');
        Schema::dropIfExists('appointment_types');
    }
}
