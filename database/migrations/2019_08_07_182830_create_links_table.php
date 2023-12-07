<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->bigIncrements('id');
//            $table->integer('sort_id')->nullable();
            $table->string('representative')->nullable();
            $table->string('web_address')->nullable();
            $table->string('email')->nullable();
            $table->string('notes', 60)->nullable();
            $table->string('office_phone')->nullable();
            $table->string('extension')->nullable();
            $table->string('cell_phone')->nullable();
            $table->boolean('active');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('category_id');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('links', function (Blueprint $table) {
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('link_logins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_name');
            $table->string('password');
            $table->unsignedBigInteger('link_id');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('link_logins', function (Blueprint $table) {
            $table->foreign('link_id')->references('id')->on('links');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_logins');
        Schema::dropIfExists('links');
    }
}
