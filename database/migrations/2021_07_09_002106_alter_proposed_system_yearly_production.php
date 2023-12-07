<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProposedSystemYearlyProduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposed_systems', function (Blueprint $table){
            $table->unsignedBigInteger('yearly_production')->nullable();
        });
        Schema::table('lead_systems', function (Blueprint $table){
            $table->unsignedBigInteger('yearly_production')->nullable();
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
