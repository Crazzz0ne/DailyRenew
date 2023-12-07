<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('state', 3)->default('all');
            $table->unsignedBigInteger('vendor_id')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->double('size', 8, 2)->default(0);
            $table->string('type');
            $table->string('path');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('training_contents', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('training_categories');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_contents');
    }
}
