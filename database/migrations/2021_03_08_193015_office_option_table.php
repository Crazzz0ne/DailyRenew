<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OfficeOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('permissions')->nullable();
            $table->string('commission_plan')->nullable();
            $table->string('roles')->nullable();
            $table->unsignedBigInteger('office_id');
            $table->string('default_role');
            $table->timestamps();
        });

        $offices = \App\Models\Office\Office::all();
        foreach ($offices as $office) {
            \App\Models\Office\OfficeOptions::create([
                'roles' => $office->roles,
                'permissions' => $office->permissions,
                'commission_plan' => $office->commission_plan,
                'default_role' => 'canvasser',
                'office_id' => $office->id
            ]);

        }
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('permissions')->default(null)->nullable();
            $table->dropColumn('commission_plan')->default(null)->nullable();
            $table->dropColumn('roles')->default(null)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_options');
    }
}
