<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMastermindCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mastermind_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('mastermind_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('state', 3)->default('all')->nullable();
            $table->unsignedBigInteger('vendor_id')->default(1)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->double('size', 8, 2)->default(0)->nullable();
            $table->boolean('approved')->nullable();
            $table->string('type')->nullable();
            $table->string('path');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('mastermind_contents', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::dropIfExists('mastermind_categories');
        Schema::dropIfExists('mastermind_contents');

    }
}
