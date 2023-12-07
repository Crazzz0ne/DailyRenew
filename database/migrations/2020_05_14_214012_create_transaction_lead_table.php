<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionLeadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

//    TODO: I would like to add this but time crunch, need to edit the LeadRepository and set things up there to grab the old data before its updated.
    public function up()
    {
        Schema::create('transaction_lead', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->string('attribute');
            $table->string('new_value');
            $table->string('old_value');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::table('transaction_lead', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_lead');
    }
}
