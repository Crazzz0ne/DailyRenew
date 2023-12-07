<?php


namespace App\Models\Transaction;


use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Database\Eloquent\Model;

class LeadTransaction extends Model
{
    protected $fillable = [
        'lead_id',
        'column',
        'data',
        'old_data',
        'user_id'
    ];

    protected $table = 'transaction_lead';


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
