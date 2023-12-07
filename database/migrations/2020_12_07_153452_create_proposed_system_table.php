<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposedSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposed_systems', function (Blueprint $table) {
            $table->integer('system_size')
                ->nullable();
            $table->unsignedBigInteger('lead_id')
                ->nullable();
            $table->double('monthly_payment')
                ->nullable()
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
            $table->unsignedBigInteger('proposal_doc_id')
                ->nullable();
            $table->unsignedBigInteger('test_doc_id')
                ->nullable();
            $table->unsignedBigInteger('rep_design_approved')
                ->nullable();
            $table->unsignedBigInteger('pb_design_approved')
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->foreign('proposal_doc_id')
                ->references('id')->on('lead_uploads');
            $table->foreign('test_doc_id')
                ->references('id')->on('lead_uploads');
            $table->foreign('rep_design_approved')
                ->references('id')->on('users');
            $table->foreign('pb_design_approved')
                ->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposed_system');
    }
}
