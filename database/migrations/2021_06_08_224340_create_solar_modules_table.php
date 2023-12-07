<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolarModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solar_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active')->default(true);
            $table->string('name');
            $table->string('model');
            $table->unsignedBigInteger('epc_id')->default(1);
            $table->string('epc_owner_id');
            $table->integer('watts');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('solar_inverters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active')->default(true);
            $table->string('name');
            $table->string('model');
            $table->unsignedBigInteger('epc_id')->default(1);
            $table->string('epc_owner_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('epc_finances', function (Blueprint $table){
            $table->renameColumn('complete_id', 'epc_owner_id');
        });

        Schema::table('leads', function (Blueprint $table){
            $table->string('epc_owner_id')->nullable()->default(null);
        });

        Schema::table('power_companies', function (Blueprint $table){
            $table->renameColumn('complete_id', 'epc_owner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solar_modules');
    }
}
