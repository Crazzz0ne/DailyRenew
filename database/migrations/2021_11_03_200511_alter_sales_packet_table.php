<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesPacketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_packets', function (Blueprint $table){
            $table->dateTime('submitted_for_permitting_date')->default(null)->nullable();
            $table->dateTime('permitting_received_date')->default(null)->nullable();
            $table->dateTime('design_plan_sent_date')->default(null)->nullable();
        });
        Schema::table('customers', function (Blueprint $table){
            $table->date('dob')->default(null)->nullable();
            $table->string('last_four')->default(null)->nullable();
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
