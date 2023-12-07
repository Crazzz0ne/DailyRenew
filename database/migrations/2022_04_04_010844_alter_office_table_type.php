<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOfficeTableType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('type')->default('default');
        });
        Schema::table('appointment_availability', function (Blueprint $table) {
            $table->string('type')->default('call-center');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('appointment_availability', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
