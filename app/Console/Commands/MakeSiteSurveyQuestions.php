<?php

namespace App\Console\Commands;

use App\Models\Epc\CompleteSiteSurveyQuestions;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Console\Command;

class MakeSiteSurveyQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createSiteSurveyQuestions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create site survey questions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $leads = Lead::where('site_survey_question_id', null)->get();

        foreach ($leads as $lead) {
//           create a new site survey option and attach it to the lead
         $siteSurveyQuestion =  new CompleteSiteSurveyQuestions([
            ]);
            $lead->siteSurveyQuestions()->save($siteSurveyQuestion);

        }

    }
}
