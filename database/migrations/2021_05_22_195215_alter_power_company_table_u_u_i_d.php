<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPowerCompanyTableUUID extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('power_companies', function (Blueprint $table) {
            $table->string('complete_id')->default(null)->after('epc_id');
        });


        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->decimal('contract_amount', '8', '2')->default(null)->after('epc_system_id');
            $table->renameColumn('test_doc_id', 'savings_doc_id');
        });
        Schema::table('lead_systems', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_amount')->default(null)->after('epc_system_id');
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
