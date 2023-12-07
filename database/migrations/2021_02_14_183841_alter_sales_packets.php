<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesPackets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_packets', function (Blueprint $table) {
            $table->boolean('sat')->default(false);
        });

        Schema::create('commission_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('office_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->boolean('type_id')->default(false);
            $table->double('amount', 5,2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('commission_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('office_id');
            $table->double('amount', 7,2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('commissions');
            $table->double('amount', 8,2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('round_robins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('list');
            $table->string('type');
            $table->unsignedBigInteger('office_id');
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
