<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('queues', function (Blueprint $table) {
           $table->boolean('urgent')->after('type')->default(false);

        });

        Schema::table('commission_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('approved')->default(null)->nullable();
        });

        Schema::table('markets', function (Blueprint $table) {
            $table->string('permissions')->default(null)->nullable();
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->string('permissions')->default(null)->nullable();
            $table->string('commission_plan')->default(null)->nullable();
            $table->string('roles')->default(null)->nullable();
        });



//        $queues = \App\Models\SalesFlow\Lead\Line::all();
//
//        foreach ($queues as $queue) {
//            $queue->queuetable_id = $queue->lead_id;
//            $queue->queuetable_type = 'App\\Models\\SalesFlow\\Lead\\Lead';
//            $queue->save();
//        }
//
//
//        Schema::table('queues', function (Blueprint $table) {
//            $table->dropForeign('queues_lead_id_foreign');
//            $table->dropColumn('lead_id')->default(null)->nullable();
//        });
    }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public
        function down()
        {
            //
        }
    }
