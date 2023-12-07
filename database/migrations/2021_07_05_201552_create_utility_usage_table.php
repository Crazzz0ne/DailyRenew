<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilityUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_usages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('utility_id');
            $table->decimal('jan_kwh', 6, 2)->default(0);
            $table->decimal('jan_bill', 6, 2)->default(0);
            $table->decimal('feb_kwh', 6, 2)->default(0);
            $table->decimal('feb_bill', 6, 2)->default(0);
            $table->decimal('mar_kwh', 6, 2)->default(0);
            $table->decimal('mar_bill', 6, 2)->default(0);
            $table->decimal('apr_kwh', 6, 2)->default(0);
            $table->decimal('apr_bill', 6, 2)->default(0);
            $table->decimal('may_kwh', 6, 2)->default(0);
            $table->decimal('may_bill', 6, 2)->default(0);
            $table->decimal('jun_kwh', 6, 2)->default(0);
            $table->decimal('jun_bill', 6, 2)->default(0);
            $table->decimal('jul_kwh', 6, 2)->default(0);
            $table->decimal('jul_bill', 6, 2)->default(0);
            $table->decimal('aug_kwh', 6, 2)->default(0);
            $table->decimal('aug_bill', 6, 2)->default(0);
            $table->decimal('sep_kwh', 6, 2)->default(0);
            $table->decimal('sep_bill', 6, 2)->default(0);
            $table->decimal('oct_kwh', 6, 2)->default(0);
            $table->decimal('oct_bill', 6, 2)->default(0);
            $table->decimal('nov_kwh', 6, 2)->default(0);
            $table->decimal('nov_bill', 6, 2)->default(0);
            $table->decimal('dec_kwh', 6, 2)->default(0);
            $table->decimal('dec_bill', 6, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utlitys_usage');
    }
}
