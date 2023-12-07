<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOfficeCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('commission_types', function (Blueprint $table){
            $table->unsignedBigInteger('pay_rate_id')->nullable()->default(null);
        });

        Schema::table('offices', function (Blueprint $table){
            $table->unsignedBigInteger('commission_plan_id')->nullable()->default(null);
            $table->smallInteger('salary')->nullable()->default(null);
        });

        Schema::table('office_commissions', function (Blueprint $table){
            $table->unsignedBigInteger('pay_rate_id')->nullable()->default(null);
        });

        Schema::table('users', function (Blueprint $table){
            $table->unsignedBigInteger('pay_rate_id')->nullable()->default(null);
        });

        Schema::create('commission_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 120);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pay_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 120);
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
        //
    }
}
