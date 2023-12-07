<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerEfficienciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_efficiencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('canvassaers_openers_closers_avg');
            $table->integer('manager_avg');
            $table->integer('other');
            $table->boolean('truth')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('manager_efficiencies', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('office_id')->references('id')->on('offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manager_efficiencies');
    }
}
