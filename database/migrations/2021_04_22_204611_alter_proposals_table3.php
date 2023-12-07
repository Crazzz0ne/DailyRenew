<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProposalsTable3 extends Migration
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
            $table->decimal('system_size', 6, 2)->change();
            $table->decimal('solar_rate', 4, 3)->change();
            $table->decimal('monthly_payment', 6, 2)->change();
        });

        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->decimal('system_size', 6, 2)->change();
            $table->decimal('solar_rate', 4, 3)->change();
            $table->decimal('monthly_payment', 6, 2)->change();
        });

        Schema::table('lead_systems', function (Blueprint $table) {
            $table->decimal('system_size', 6, 2)->change();
            $table->decimal('solar_rate', 4, 3)->change();
            $table->decimal('monthly_payment', 6, 2)->change();
        });

        foreach ($requestedSystems as $requested) {
            $newSolarRate = null;
            $newSystemSize = null;
            if ($requested->solar_rate) {
                $newSolarRate = $requested->solar_rate / 100;
            }
            if ($requested->system_size) {
                $newSystemSize = $requested->system_size / 1000;
            }
            $requested->update([
                'solar_rate' => $newSolarRate,
                'system_size' => $newSystemSize,
            ]);
        }
        foreach ($proposedSystems as $requested) {
            $newSolarRate = null;
            $newSystemSize = null;
            if ($requested->solar_rate) {
                $newSolarRate = $requested->solar_rate / 100;
            }
            if ($requested->system_size) {
                $newSystemSize = $requested->system_size / 1000;
            }
            $requested->update([
                'solar_rate' => $newSolarRate,
                'system_size' => $newSystemSize,
            ]);
        }
        foreach ($leadSystems as $requested) {
            $newSolarRate = null;
            $newSystemSize = null;
            if ($requested->solar_rate) {
                $newSolarRate = $requested->solar_rate / 100;
            }
            if ($requested->system_size) {
                $newSystemSize = $requested->system_size / 1000;
            }
            $requested->update([
                'solar_rate' => $newSolarRate,
                'system_size' => $newSystemSize,
            ]);
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
