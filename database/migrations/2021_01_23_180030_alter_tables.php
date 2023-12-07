<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $leads = \App\Models\SalesFlow\Lead\Lead::all();
        foreach ($leads as $lead) {

            if ($lead->epc === 'Sunrun') {
                $lead->epc_id = 1;
            } elseif ($lead->epc === 'la solar') {
                $lead->epc_id = 2;
            }


            if ($lead->credit_status === 'credit not run' && $lead->epc === 'la solar') {
                $lead->credit_status_id = 4;
            } elseif ($lead->credit_status === 'credit not ran' && $lead->epc === 'Sunrun') {
                $lead->credit_status_id = 1;
            } elseif ($lead->credit_status === 'Tier I' && $lead->epc === 'la solar') {
                $lead->credit_status_id = 6;
            } elseif ($lead->credit_status === 'Tier II' && $lead->epc === 'la solar') {
                $lead->credit_status_id = 5;
            } elseif ($lead->credit_status === 'fail' && $lead->epc === 'la solar') {
                $lead->credit_status_id = 7;
            } elseif ($lead->credit_status === 'fail' && $lead->epc === 'Sunrun') {
                $lead->credit_status_id = 8;
            } elseif ($lead->credit_status === 'pass' && $lead->epc === 'Sunrun') {
                $lead->credit_status_id = 2;
            } elseif ($lead->credit_status === 'credit not run' && $lead->epc === 'Sunrun') {
                $lead->credit_status_id = 1;
            } elseif ($lead->credit_status === 'manual' && $lead->epc === 'Sunrun') {
                $lead->credit_status_id = 3;
            }

            if ($lead->status === 'pending paperwork' && $lead->status === 'pending paperwork') {
                $lead->status = 'Credit Pass';
            }

            if ($lead->integrations_approved) {
                $lead->integrations_approved = 3;
            } elseif ($lead->integrations_approved === null) {
                $lead->integrations_approved = 1;
            } else {
                $lead->integrations_approved = 2;
            }

            if ($lead->status === 'pending credit approval') {
                $lead->status = 'Pending Credit Check';
            }

            $lead->save();


        }

        $leadSystems = \App\Models\SalesFlow\Lead\System\System::where('external_proposal_id', '=', null)
            ->get();

        foreach ($leadSystems as $system) {

            $lead = \App\Models\SalesFlow\Lead\Lead::where('system_id', '=', $system->id)
                ->withTrashed()
                ->update(['system_id' => null]);

            $system->forceDelete();


        }


        Schema::table('leads', function (Blueprint $table) {
            $table->integer('integrations_approved')->default(1)->change();


//            $table->dropColumn(['credit_status']);
        });


        Schema::table('queues', function (Blueprint $table) {
            $table->softDeletes()->after('filled_time');

        });

        $lines = \App\Models\SalesFlow\Lead\Line::all();

        foreach ($lines as $line) {
            if ($line->type === 'credit app') {
                $line->type = 'credit_app';
            } else if ($line->type === 'Proposal Builder') {
                $line->type = 'proposal builder';
            }
            $line->save();
        }

        $utlities = \App\Models\SalesFlow\Lead\LeadUtility::all();

        foreach ($utlities as $utility) {
            switch ($utility->rate_plan) {
                case 'TOU -D-4-9':
                    $utility->power_plan_id = 1;
                    break;
                case 'TOU -D-5-8':
                    $utility->power_plan_id = 2;
                    break;
                case 'TOU -D-Prime':
                    $utility->power_plan_id = 3;
                    break;
                case 'Domestic':
                    $utility->power_plan_id = 4;
                    break;
                case 'D-CARE':
                    $utility->power_plan_id = 5;
                    break;
                case 'TOU DR-P':
                    $utility->power_plan_id = 14;
                    break;
                case 'TOU-DR1':
                    $utility->power_plan_id = 12;
                    break;
                case 'TOU-DR2':
                    $utility->power_plan_id = 13;
                    break;
            }

            switch ($utility->power_discount_plan) {
                case 'SDP':
                    $utility->power_discount_id = 7;
                    break;
                case 'Medical Baseline':
                    $utility->power_discount_id = 8;
                    break;
                case 'Care':
                    if ($utility->power_company === 'SCE') {
                        $utility->power_discount_id = 9;
                    }elseif ($utility->power_company === 'SDGE'){
                        $utility->power_discount_id = 16;
                    }
                    $utility->power_discount_id = 9;
                    break;
                case 'Level Pay Program':
                    $utility->power_discount_id = 10;
                    break;
                case 'FERA':
                    if ($utility->power_company === 'SCE') {
                        $utility->power_discount_id = 11;
                    }elseif ($utility->power_company === 'SDGE'){
                        $utility->power_discount_id = 17;
                    }
                    break;
                case 'None':
                    if ($utility->power_company === 'SCE') {
                        $utility->power_discount_id = 15;
                    }elseif ($utility->power_company === 'SDGE'){
                        $utility->power_discount_id = 1;
                    }
                    break;


            }

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
