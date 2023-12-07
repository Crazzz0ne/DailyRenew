<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeadOfficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table){
//            $table->dropForeign('user_id');
            $table->unsignedBigInteger('origin_office_id')->nullable();
            $table->dropColumn('status');
//            $table->dropColumn('user_id');
            $table->dropColumn('jeopardy');
//            $table->dropColumn('proposed_system_id');
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
