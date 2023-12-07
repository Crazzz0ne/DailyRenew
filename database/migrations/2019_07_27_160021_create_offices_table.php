<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('offices', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('phone_number')->nullable();
			$table->string('address')->nullable();
			$table->string('zip_code')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('email')->nullable();
			$table->unsignedBigInteger('market_id');
			$table->dateTime('deleted_at')->nullable();
			$table->timestamps();
		});


        Schema::create('markets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('abbreviation');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
        Schema::table('offices', function (Blueprint $table) {
            $table->foreign('market_id')->references('id')->on('markets');
        });

        Schema::create('market_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });


        Schema::create('market_has_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('market_id');
            $table->unsignedBigInteger('rule_id');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
//
        Schema::table('market_has_rules', function (Blueprint $table) {
            $table->foreign('market_id')->references('id')->on('markets');
            $table->foreign('rule_id')->references('id')->on('market_rules');
        });

//
        Schema::create('power_companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('market_id');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();

        });

        Schema::table('power_companies', function (Blueprint $table) {
            $table->foreign('market_id')->references('id')->on('markets');
        });


        Schema::create('power_company_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type');
            $table->unsignedBigInteger('power_company_id');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();

        });

        Schema::table('power_company_programs', function (Blueprint $table) {
            $table->foreign('power_company_id')->references('id')->on('power_companies');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists(config('access.table_names.password_histories'));
        Schema::dropIfExists('announcement_user');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('users');
        Schema::dropIfExists('offices');
        Schema::dropIfExists('market_has_rules');
        Schema::dropIfExists('market_rules');
        Schema::dropIfExists('power_company_programs');
        Schema::dropIfExists('office_users');
        Schema::dropIfExists('power_companies');
        Schema::dropIfExists('markets');

    }
}
