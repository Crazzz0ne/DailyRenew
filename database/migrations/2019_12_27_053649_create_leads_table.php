<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->default('no');
            $table->string('last_name')->default('name');
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('language')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('office_id')->nullable();
            $table->double('account_number')->nullable();
            $table->boolean('system_built')->default(false);
            $table->string('status')->default('new lead');
            $table->double('system_size')->default(0);
            $table->string('power_company')->nullable();
            $table->string('rate_plan')->nullable();
            $table->string('power_discount_plan')->nullable();
            $table->double('customer_cost_per_watt')->nullable();
            $table->double('cost_per_watt')->nullable();
            $table->string('fund')->nullable();
            $table->string('credit_status')->default('credit not ran');
            $table->boolean('dealer_fee')->default(false);
            $table->double('average_bill')->default(0)->nullable();
            $table->boolean('utility_bill')->default(false)->nullable();
            $table->boolean('solar_agreement_signed')->default(false)->nullable();
            $table->boolean('nem_doc_signed')->default(false)->nullable();
            $table->boolean('cpuc_doc_signed')->default(false)->nullable();
            $table->date('close_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lead_logins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->longText('user_name');
            $table->string('password');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->longText('note');
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lead_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->string('type');
            $table->string('path');
            $table->double('size')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('user_has_leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('position_id');
            $table->timestamps();
        });
        Schema::create('lead_referrals', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->tinyInteger('warm')->default(0);
            $table->timestamps();
        });

        Schema::table('lead_referrals', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('lead_logins', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads');
        });


        Schema::table('user_has_leads', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('position_id')->references('id')->on('positions');
        });

        Schema::table('lead_notes', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('office_id')->references('id')->on('offices');
        });

        Schema::table('lead_uploads', function (Blueprint $table) {
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->foreign('user_id')->references('id')->on('users');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('lead_referrals');
        Schema::dropIfExists('lead_logins');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('user_has_leads');
        Schema::dropIfExists('lead_uploads');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('customers');

    }
}
