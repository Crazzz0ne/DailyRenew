<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMassTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mass_texts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_number');
            $table->string('customer_name');
            $table->string('sending_number');
            $table->string('rep_name');
            $table->boolean('sent')->default(false);
            $table->dateTime('sent_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mass_texts');
    }
}
