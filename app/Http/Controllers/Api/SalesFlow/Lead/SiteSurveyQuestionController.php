<?php

namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Http\Controllers\Controller;
use App\Models\Epc\CompleteSiteSurveyQuestions;
use Illuminate\Http\Request;

class SiteSurveyQuestionController extends Controller
{

    public function update(CompleteSiteSurveyQuestions $completeSiteSurveyQuestions, Request $request)
    {
        $completeSiteSurveyQuestions->update($request->all());
        return response()->json($completeSiteSurveyQuestions);
    }

}
