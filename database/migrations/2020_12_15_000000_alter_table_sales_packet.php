<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class AlterTableSalesPacket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('sales_packets', function (Blueprint $table) {
            $table->dropColumn(['test_contract', 'quote']);
        });

        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->BigIncrements('id')->first();

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
