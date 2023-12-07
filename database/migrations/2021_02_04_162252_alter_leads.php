<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('lead_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'New Lead',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Pending Credit Check',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Credit Pass',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Usage Unavailable',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Negotiating System',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Pending Paper Work',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Pending Site Survey',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Site Survey',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Change Order',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Pending Site Plan Signed',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Pending Install',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Installed',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'PTO',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Cancelled Appointment',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Failed Credit',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Job In Jeopardy',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Lost',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Low Usage',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Missed Opportunity',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'No Show',
        ]);

        \App\Models\SalesFlow\Lead\LeadStatus::create([
            'name' => 'Cancelled',
        ]);

        Schema::table('leads', function (Blueprint $table) {
            $table->string('status')->change()->default('New Lead');
            $table->dropColumn('credit_status');
            $table->dropColumn('editing');
            $table->dropColumn('epc');
            $table->unsignedBigInteger('status_id')->after('id')->default(1);
            $table->unsignedBigInteger('jeopardy_id')->after('status_id')->nullable()->default(null);
        });

        $leads = \App\Models\SalesFlow\Lead\Lead::all();
        foreach ($leads as $lead) {
            switch ($lead->status) {
                case 'New Lead':
                    $lead->status_id = 1;
                    break;
                case 'new lead':
                    $lead->status_id = 1;
                    break;
                case 'Pending Credit Check':
                    $lead->status_id = 2;
                    break;
                case 'Credit Pass':
                    $lead->status_id = 3;
                    break;
                case 'Negotiating System':
                    $lead->status_id = 5;
                    break;
                case 'Pending Site Survey':
                    $lead->status_id = 7;
                    break;
                case 'site survey':
                    $lead->status_id = 8;
                    break;
                case 'Site Survey':
                    $lead->status_id = 8;
                    break;
                case 'Pending Site Plan Signed':
                    $lead->status_id = 10;
                    break;
                case 'PTO':
                    $lead->status_id = 13;
                    break;
                case 'pending paper work':
                    $lead->status_id = 6;
                    break;
                case 'Pending Install Date':
                    $lead->status_id = 11;
                    break;
                case 'pending install':
                    $lead->status_id = 11;
                    break;
                case 'Pending Install':
                    $lead->status_id = 11;
                    break;
                case 'No Show':
                    $lead->status_id = 20;
                    break;
                case 'missed opportunity':
                    $lead->status_id = 19;
                    break;
                case 'Low Usage':
                    $lead->status_id = 18;
                    break;
                case 'lost':
                    $lead->status_id = 17;
                    break;
                case 'Lost':
                    $lead->status_id = 17;
                    break;
                case 'Job In Jeopardy':
                    $lead->status_id = 16;
                    break;
                case 'Job in Jeopardy':
                    $lead->status_id = 16;
                    break;
                case 'Installed':
                    $lead->status_id = 12;
                    break;
                case 'Failed Credit':
                    $lead->status_id = 15;
                    break;
                case 'Cancelled Appointment':
                    $lead->status_id = 14;
                    break;

            }
            switch (strtolower($lead->jeopardy)) {

                case 'new lead':
                    $lead->jeopardy_id = 1;
                    break;
                case 'pending credit check':
                    $lead->jeopardy_id = 2;
                    break;
                case 'credit pass':
                    $lead->jeopardy_id = 3;
                    break;
                case 'negotiating system':
                    $lead->jeopardy_id = 5;
                    break;
                case 'pending site survey':
                    $lead->jeopardy_id = 7;
                    break;
                case 'site survey':
                    $lead->jeopardy_id = 8;
                    break;
                case 'pending site plan signed':
                    $lead->jeopardy_id = 10;
                    break;
                case 'pto':
                    $lead->jeopardy_id = 13;
                    break;
                case 'pending paper work':
                    $lead->jeopardy_id = 6;
                    break;
                case 'pending install date':
                    $lead->jeopardy_id = 11;
                    break;
                case 'pending install':
                    $lead->jeopardy_id = 11;
                    break;
                case 'no show':
                    $lead->jeopardy_id = 20;
                    break;
                case 'missed opportunity':
                    $lead->jeopardy_id = 19;
                    break;
                case 'low usage':
                    $lead->jeopardy_id = 18;
                    break;
                case 'lost':
                    $lead->jeopardy_id = 17;
                    break;
                case 'installed':
                    $lead->jeopardy_id = 12;
                    break;
                case 'failed credit':
                    $lead->jeopardy_id = 15;
                    break;
                case 'cancelled appointment':
                    $lead->jeopardy_id = 14;
                    break;
                default:
                    $lead->jeopardy_id = null;
                    break;

            }
            $lead->save();
        }

        Schema::table('sales_packets', function (Blueprint $table) {
            $table->timestamp('submitted')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_status');
    }
}
