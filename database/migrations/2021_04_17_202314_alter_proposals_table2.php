<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProposalsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('epc_equipment', function (Blueprint $table) {
            $table->string('complete_id')->default(null)->after('cost');
            $table->boolean('flat')->default(false)->after('cost');
        });
        Schema::table('epc_finances', function (Blueprint $table) {
            $table->string('complete_id')->default(null)->after('fee');
        });

        Schema::table('requested_systems', function (Blueprint $table) {
            $table->unsignedBigInteger('modules_id')->default(null)->after('epc_system_id');
            $table->unsignedTinyInteger('modules_count')->default(null)->after('epc_system_id');
            $table->unsignedBigInteger('inverter_id')->default(null)->after('epc_system_id');
        });
        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->unsignedBigInteger('modules_id')->default(null)->after('epc_system_id');
            $table->unsignedTinyInteger('modules_count')->default(null)->after('epc_system_id');
            $table->unsignedBigInteger('inverter_id')->default(null)->after('epc_system_id');
        });
        Schema::table('lead_systems', function (Blueprint $table) {
            $table->unsignedBigInteger('modules_id')->default(null)->after('epc_system_id');
            $table->unsignedTinyInteger('modules_count')->default(null)->after('epc_system_id');
            $table->unsignedBigInteger('inverter_id')->default(null)->after('epc_system_id');
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
