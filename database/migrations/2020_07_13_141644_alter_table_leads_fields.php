<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLeadsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function ($table) {
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->after('editing')
                ->default(null);
            $table->unsignedBigInteger('name_on_bill')
                ->nullable()
                ->after('customer_id')
                ->default(null);
            $table->string('epc')
                ->after('fund')
                ->nullable()
                ->default(null);
            $table->date('install_date')
                ->after('cpuc_doc_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('site_survey_date')
                ->after('cpuc_doc_signed')
                ->nullable()
                ->default(null);
            $table->boolean('deal_submitted')
                ->after('cpuc_doc_signed')
                ->default(false);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
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
