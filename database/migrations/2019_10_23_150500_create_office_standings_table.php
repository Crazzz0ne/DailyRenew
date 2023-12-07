<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficeStandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_standings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->date('sdate');
            $table->unsignedBigInteger('user_id');
            $table->boolean('approved')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('office_standings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('office_standings_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_standing_id');
            $table->unsignedBigInteger('office_id');
            $table->string('name')->default('N/A');
            $table->float('data')->default(0);
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('office_standings_data', function (Blueprint $table) {
            $table->foreign('office_standing_id')->references('id')->on('office_standings');
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
        Schema::dropIfExists('office_standings_data');
        Schema::dropIfExists('office_standings');
    }
}
