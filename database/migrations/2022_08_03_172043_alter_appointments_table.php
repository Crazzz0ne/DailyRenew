<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('status_id')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'completed_at', 'status_id']);
        });
        Schema::table('appointment_statuses', function (Blueprint $table) {
            $table->drop();
        });
    }
}
