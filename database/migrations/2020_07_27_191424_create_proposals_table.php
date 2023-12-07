<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUtility;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_proposals', function ($table) {
            $table->bigIncrements('id');
            $table->string('his_license')
                ->nullable();
            $table->integer('system_size')
                ->nullable();
            $table->integer('monthly_payment')
                ->default(null);
            $table->string('credit_score')
                ->default(null);
            $table->string('adders')
                ->default(null);
            $table->dateTime('needed_by')
                ->nullable();
            $table->string('system')
                ->nullable();
            $table->integer('offset')
                ->nullable();
            $table->integer('solar_rate')
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lead_systems', function ($table) {
            $table->bigIncrements('id');
            $table->integer('system_size')
                ->nullable();
            $table->double('monthly_payment')
                ->default(null);
            $table->integer('solar_rate')
                ->nullable();
            $table->double('offset')
                ->nullable();
            $table->string('adders')
                ->nullable();
            $table->string('system')
                ->nullable();
            $table->string('external_proposal_id')
                ->nullable();
            $table->date('design_approved')
                ->nullable()
                ->default(null);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sales_packets', function ($table) {
            $table->bigIncrements('id');
            $table->dateTime('ach_doc_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('converted')
                ->nullable()
                ->default(null);
            $table->dateTime('credit_doc_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('solar_agreement_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('proposal_doc_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('nem_doc_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('cpuc_doc_signed')
                ->nullable()
                ->default(null);
            $table->dateTime('test_contract')
                ->nullable()
                ->default(null);
            $table->text('site_survey_note')
                ->nullable()
                ->default(null);
            $table->date('quote')
                ->nullable()
                ->default(null);
            $table->dateTime('site_plan')
                ->nullable()
                ->default(null);
            $table->date('pto')
                ->nullable()
                ->default(null);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lead_utilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('kw_year_usage')
                ->nullable()
                ->default(null);
            $table->string('power_company')
                ->nullable()
                ->default(null);
            $table->string('rate_plan')
                ->nullable()
                ->default(null);
            $table->string('power_discount_plan')
                ->nullable()
                ->default(null);
            $table->double('average_bill')
                ->nullable()
                ->default(null);
            $table->string('name_on_bill')
                ->nullable()
                ->default(null);
            $table->softDeletes();

            $table->timestamps();
        });

        Schema::table('customers', function ($table) {
            $table->string('spouse_name')
                ->after('last_name')
                ->nullable()
                ->default(null);

        });

        $leads = Lead::all();

        foreach ($leads as $lead) {
            $leadUtility = new LeadUtility();
            $leadUtility->power_company = $lead->power_company;
            $leadUtility->rate_plan = $lead->rate_plan;
            $leadUtility->average_bill = $lead->average_bill;
            $leadUtility->save();


            $salesPacket = new \App\Models\SalesFlow\Lead\SalesPacket();
            $salesPacket->save();

            $lead_systems = new \App\Models\SalesFlow\Lead\System();
            $lead_systems->save();

            $lead_proposals = new \App\Models\SalesFlow\Lead\RequestedSystem();
            $lead_proposals->save();
        }

        Schema::table('leads', function ($table) {

            $table->boolean('request_builder')
                ->after('request_sp1')
                ->default(false);
            $table->unsignedBigInteger('utility_id')
                ->after('id');
            $table->unsignedBigInteger('sales_packet_id')
                ->after('id');
            $table->string('jeopardy')
                ->after('status')
                ->nullable()
                ->default(null);
            $table->unsignedBigInteger('system_id')
                ->after('id');
            $table->unsignedBigInteger('proposal_id')
                ->after('id');


            $table->dropColumn('account_number');
            $table->dropColumn('customer_cost_per_watt');
            $table->dropColumn('system_built');
            $table->dropColumn('power_company');
            $table->dropColumn('rate_plan');
            $table->dropColumn('name_on_bill');
            $table->dropColumn('system_size');
            $table->dropColumn('fund');
            $table->dropColumn('cost_per_watt');
            $table->dropColumn('power_discount_plan');
            $table->dropColumn('dealer_fee');

            $table->dropColumn('average_bill');
            $table->dropColumn('solar_agreement_signed');
            $table->dropColumn('nem_doc_signed');
            $table->dropColumn('cpuc_doc_signed');
            $table->dropColumn('utility_bill');
            $table->dropColumn('site_survey_date');
            $table->dropColumn('install_date');
            $table->dropColumn('deal_submitted');

        });

        foreach ($leads as $lead) {
            $lead->utility_id = $lead->id;
            $lead->sales_packet_id = $lead->id;
            $lead->system_id = $lead->id;
            $lead->proposal_id = $lead->id;
            $lead->save();
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('utility_id')
                ->references('id')->on('lead_utilities')
                ->onDelete('cascade');
            $table->foreign('sales_packet_id')
                ->references('id')->on('sales_packets')
                ->onDelete('cascade');
            $table->foreign('system_id')
                ->references('id')->on('lead_systems')
                ->onDelete('cascade');
            $table->foreign('proposal_id')
                ->references('id')->on('lead_proposals')
                ->onDelete('cascade');

        });


//        Schema::table('leads', function (Blueprint $table) {
//            $table->foreign('utility_id')->references('id')->on('lead_utilities');
//            $table->foreign('sales_packet_id')->references('id')->on('lead_utilities');
//            $table->foreign('lead_system_id')->references('id')->on('lead_utilities');
//            $table->foreign('lead_proposal_id')->references('id')->on('lead_utilities');
//
//        });


        Schema::table('users', function ($table) {
            $table->unsignedBigInteger('office_id')
                ->after('last_name')
                ->default(1);
            $table->string('his_license')
                ->after('last_name')
                ->nullable();
        });

        Schema::create('user_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('path');
            $table->double('size')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('user_uploads', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');

        });


        Schema::table('users', function (Blueprint $table) {
            $table->foreign('office_id')->references('id')->on('offices');
        });


        $officeUser = \App\Models\Office\OfficeUser::all();

        foreach ($officeUser as $office) {
            $user = \App\Models\Auth\User::where('id', '=', $office->user_id)->get()->first();
            if ($user){

                $user->office_id = $office->office_id;
                $user->save();
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

//        Schema::table('leads', function ($table) {
//
//            $table->dropForeign(['utility_id']);
//            $table->dropColumn('utility_id');
//            $table->dropForeign(['sales_packet_id']);
//            $table->dropColumn('sales_packet_id');
//            $table->dropForeign(['lead_system_id']);
//            $table->dropColumn('lead_system_id');
//            $table->dropForeign(['lead_proposal_id']);
//            $table->dropColumn('lead_proposal_id');
//        });


        Schema::dropIfExists('user_uploads');

        Schema::dropIfExists('lead_utilities');
        Schema::dropIfExists('lead_proposals');
        Schema::dropIfExists('lead_systems');
        Schema::dropIfExists('sales_packets');


    }
}



