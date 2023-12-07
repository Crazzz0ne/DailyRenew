<?php

namespace App\Models\Epc;

use Illuminate\Database\Eloquent\Model;

class CompleteSiteSurveyQuestions extends Model
{
    protected $guarded = [];


    public function lead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\SalesFlow\Lead\Lead::class);
    }

}
