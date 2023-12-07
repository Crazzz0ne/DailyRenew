<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLeadsSelfgen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function ($table) {
            $table->string('source')
                ->nullable()
                ->after('id')
                ->default('canvasser');
            $table->boolean('request_integrations')
                ->after('status')
                ->default(false);
            $table->boolean('request_sp1')
                ->after('request_integrations')
                ->default(false);
        });

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
