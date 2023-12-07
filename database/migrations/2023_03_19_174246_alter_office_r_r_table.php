<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOfficeRRTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Offices table
        Schema::table('offices', function (Blueprint $table) {
            $table->boolean('feed_global')->default(false)->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Offices table
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('feed_global');
        });
    }
}
