<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $requestedSystems = \App\Models\SalesFlow\Lead\System\RequestedSystem::all();

        $proposedSystems = \App\Models\SalesFlow\Lead\System\ProposedSystem::all();

        $leadSystems = \App\Models\SalesFlow\Lead\System\System::all();

        Schema::table('requested_systems', function (Blueprint $table) {
            $table->unsignedInteger('solar_rate')->change();
        });

        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->unsignedInteger('solar_rate')->change();
        });

        Schema::table('lead_systems', function (Blueprint $table) {
            $table->unsignedInteger('solar_rate')->change();
        });

        foreach ($requestedSystems as $requested){
            $requested->update(['solar_rate' => $requested->solar_rate *100]);
        }
        foreach ($proposedSystems as $requested){
            $requested->update(['solar_rate' => $requested->solar_rate *100]);
        }
        foreach ($leadSystems as $requested){
            $requested->update(['solar_rate' => $requested->solar_rate *100]);
        }

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
