<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompleteSiteSurveyQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('complete_site_survey_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->enum('un_permitted_work', ['yes', 'no'])->default('no');
            $table->enum('alarms_working', ['yes', 'no'])->default('no');
            $table->enum('access_issues', ['yes', 'no'])->default('no');
            $table->text('access_issues_details')->nullable();
            $table->enum('tree_removal', ['yes', 'no'])->default('no');
            $table->enum('promises', ['yes', 'no'])->default('no');
            $table->text('promises_details')->nullable();
            $table->boolean('questions_confirmed')->default(false);
            $table->timestamps();
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('site_survey_question_id')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complete_site_survey_questions');
        Schema::table('leads', function ($table) {
            $table->dropColumn('site_survey_question_id');
        });
    }
}
