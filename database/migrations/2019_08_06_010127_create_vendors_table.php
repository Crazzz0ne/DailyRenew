<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
	        $table->bigIncrements('id');
            $table->string('company_name');
            $table->string('web_address')->nullable();
            $table->string('picture')->default('solar-panel.png');
            $table->boolean('is_active')->default(1);
            $table->dateTime('deleted_at')->nullable();
//	        $table->string('address');
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
        Schema::dropIfExists('vendors');
    }
}
