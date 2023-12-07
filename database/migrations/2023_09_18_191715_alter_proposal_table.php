<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProposalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::
        table('proposed_systems', function (Blueprint $table) {
            $table->string('epc')->default('freedom');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a destructive migration.  It will delete all data in the proposed_systems table.
        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->dropColumn('epc');
        });

    }
}
