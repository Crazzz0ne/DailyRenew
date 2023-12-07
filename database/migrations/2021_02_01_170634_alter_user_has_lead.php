<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserHasLead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_leads', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('requested_systems', function (Blueprint $table) {
            $table->decimal('solar_rate', 4, 3)->default(null)->nullable()->change();
        });

        Schema::table('proposed_systems', function (Blueprint $table) {
            $table->decimal('solar_rate', 4, 3)->default(null)->nullable()->change();
        });

        Schema::table('lead_systems', function (Blueprint $table) {
            $table->decimal('solar_rate', 4, 3)->default(null)->nullable()->change();
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
