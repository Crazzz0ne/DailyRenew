<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUltUsage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utility_usages', function (Blueprint $table){
            $table->decimal('jan_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('jan_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('feb_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('feb_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('mar_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('mar_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('apr_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('apr_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('may_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('may_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('jun_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('jun_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('jul_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('jul_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('aug_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('aug_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('sep_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('sep_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('oct_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('oct_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('nov_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('nov_bill', 6, 2)->default(null)->nullable()->change();
            $table->decimal('dec_kwh', 6, 2)->default(null)->nullable()->change();
            $table->decimal('dec_bill', 6, 2)->default(null)->nullable()->change();
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
