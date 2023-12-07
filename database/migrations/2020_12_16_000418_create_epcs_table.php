<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epcs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('epc_equipment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('epc_id');
            $table->boolean('active')->default(true);
            $table->string('name');
            $table->string('type');
            $table->double('cost', '7', '2')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('epc_id')
                ->references('id')->on('epcs')->onDelete('cascade');
        });

        Schema::create('epc_adders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('flat_cost')->default(true);
            $table->double('cost', '7', '2')->nullable();
            $table->unsignedBigInteger('epc_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('epc_id')
                ->references('id')->on('epcs')->onDelete('cascade');
        });

        Schema::create('epc_credit_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('epc_id');
            $table->string('name');

            $table->timestamps();
            $table->foreign('epc_id')
                ->references('id')->on('epcs')->onDelete('cascade');
        });


        Schema::create('epc_finances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('epc_id');
            $table->string('name');
            $table->double('rate', '4', '2');
            $table->integer('term')->default(20);
            $table->double('fee', '4', '2')->default(0);

            $table->timestamps();
            $table->foreign('epc_id')
                ->references('id')->on('epcs')->onDelete('cascade');
        });
        Schema::rename('lead_proposals', 'requested_systems');

        Schema::table('requested_systems', function (Blueprint $table) {

            $table->dropColumn(['his_license', 'credit_score']);
            $table->unsignedBigInteger('epc_system_id')
                ->after('id')->nullable();

            $table->unsignedBigInteger('monthly_payment')
                ->change()
                ->default(null)
                ->nullable();

            $table->unsignedBigInteger('epc_finance_id')
                ->after('id')->nullable();

            $table->unsignedBigInteger('lead_id')
                ->after('id')->nullable();
            $table->double('ppw', '4', '2')->nullable();
            $table->dateTime('approved')->nullable();

            $table->integer('roof_work')->after('ppw')->nullable();

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onDelete('cascade');

            $table->foreign('epc_system_id')
                ->references('id')
                ->on('epc_equipment')
                ->onDelete('cascade');

            $table->foreign('epc_finance_id')
                ->references('id')
                ->on('epc_finances')
                ->onDelete('cascade');
        });

        Schema::table('proposed_systems', function (Blueprint $table) {

            $table->unsignedBigInteger('epc_system_id')
                ->after('id')->nullable();
            $table->foreign('epc_system_id')
                ->references('id')->on('epc_equipment')->onDelete('cascade');
            $table->double('ppw', '4', '2')->nullable();
            $table->unsignedBigInteger('epc_finance_id')
                ->after('id')->nullable();

            $table->unsignedBigInteger('requested_system_id')
                ->after('id')->nullable();

            $table->integer('roof_work')->after('ppw')->nullable();

            $table->foreign('requested_system_id')
                ->references('id')->on('requested_systems')->onDelete('cascade');


            $table->foreign('epc_finance_id')
                ->references('id')
                ->on('epc_finances')
                ->onDelete('cascade');
        });

        Schema::table('lead_systems', function (Blueprint $table) {


            $table->unsignedBigInteger('epc_finance_id')
                ->after('id')
                ->nullable();
            $table->unsignedBigInteger('epc_system_id')
                ->after('id')
                ->nullable();
            $table->double('ppw', '4', '2')
                ->after('solar_rate')
                ->nullable();

            $table->integer('roof_work')->after('ppw')->nullable();
            $table->foreign('epc_system_id')
                ->references('id')
                ->on('epc_equipment')
                ->onDelete('cascade');

            $table->foreign('epc_finance_id')
                ->references('id')
                ->on('epc_finances')
                ->onDelete('cascade');
        });

        Schema::table('power_companies', function (Blueprint $table) {
            $table->renameColumn('market_id', 'epc_id');

        });

        Schema::table('lead_utilities', function (Blueprint $table) {

            $table->unsignedBigInteger('power_company_id')->nullable();
            $table->unsignedBigInteger('power_discount_id')->nullable();
            $table->unsignedBigInteger('power_program_id')->nullable();
            $table->foreign('power_company_id')
                ->references('id')->on('power_companies')->onDelete('cascade');
            $table->foreign('power_discount_id')
                ->references('id')->on('power_company_programs')->onDelete('cascade');
            $table->foreign('power_program_id')
                ->references('id')->on('power_company_programs')->onDelete('cascade');
        });


        Schema::create('requested_system_adders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('cost', '7', '2')->nullable();
            $table->unsignedBigInteger('epc_adder_id')->nullable();
            $table->unsignedBigInteger('proposed_system_id')->nullable();

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('epc_adder_id')
                ->references('id')->on('epc_adders');

            $table->foreign('proposed_system_id')
                ->references('id')->on('proposed_systems')->onDelete('cascade');
        });

        Schema::create('proposed_system_adders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('cost', '7', '2')->nullable();
            $table->unsignedBigInteger('epc_adder_id')->nullable();;
            $table->unsignedBigInteger('proposed_system_id');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('epc_adder_id')
                ->references('id')->on('epc_adders')->onDelete('cascade');

            $table->foreign('proposed_system_id')
                ->references('id')->on('proposed_systems')->onDelete('cascade');
        });

        Schema::create('system_adders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('cost', '7', '2')->nullable();
            $table->unsignedBigInteger('epc_adder_id')->nullable();
            $table->unsignedBigInteger('system_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('epc_adder_id')
                ->references('id')->on('epc_adders');
            $table->foreign('system_id')
                ->references('id')->on('lead_systems');
        });

        Schema::table('leads', function (Blueprint $table) {



            $table->unsignedBigInteger('proposed_system_id')
                ->nullable()
                ->after('proposal_id');

            $table->unsignedBigInteger('system_id')
                ->change()
                ->nullable();

            $table->foreign('proposed_system_id')
                ->references('id')
                ->on('requested_systems')
                ->onDelete('cascade');


            $table->unsignedBigInteger('credit_status_id')
                ->nullable()
                ->after('customer_id');

            $table->foreign('credit_status_id')
                ->references('id')
                ->on('epc_credit_status')
                ->onDelete('cascade');

            $table->integer('integrations_approved')
                ->nullable()
                ->change();

            $table->unsignedBigInteger('epc_id')
                ->after('id')
                ->nullable();

            $table->string('epc_lead_id')->nullable()->after('epc_id');

            $table->foreign('epc_id')
                ->references('id')
                ->on('epcs')
                ->onDelete('cascade');

            $table->dropForeign(['proposal_id']);
            $table->dropColumn(['request_sp1', 'request_integrations', 'request_builder', 'proposal_id']);


        });





        Schema::table('lead_uploads', function (Blueprint $table) {
            $table->string('name')
                ->nullable()
                ->after('type');
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->boolean('require_integrations')
                ->default(false)
                ->after('name');
            $table->boolean('view_ppw')
                ->default(false)
                ->after('name');
            $table->double('default_ppw', '5', '2')
                ->nullable()
                ->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('epcs');
    }
}
