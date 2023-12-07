<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMassTexts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mass_texts', function (Blueprint $table) {
            $table->string('rep_name')->default('Shane Montana')->change();
            $table->string('email')->after('customer_name')->unique()->nullable();
            $table->string('type')->after('rep_name')->default('review');
            $table->string('customer_number')->nullable()->change();
            $table->dropColumn(['sending_number']);
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
