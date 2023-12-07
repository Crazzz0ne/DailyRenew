<?php


namespace App\Observers\SalesFlow;


use App\Models\SalesFlow\Lead\Lead;
use App\Models\Transaction\LeadTransaction;

class LeadObserver
{

    protected $except = [
        'account_number',
        'created_at',
        'updated_at'
    ];

    /**
     * Handle the Lead "created" event.
     *
     * @param Lead $lead
     * @return void
     */
    public function created(Lead $lead)
    {
        //
    }

    /**
     * Handle the Lead "updated" event.
     *
     * @param Lead $lead
     * @return void
     */
    public function updated(Lead $lead)
    {
        //
    }



    public function saved(Lead $lead)
    {
        // search for changes
        foreach ($lead->getChanges() as $key => $new_value) {

            // get original value
            $old_value = $lead->getOriginal($key);
            if (!$old_value){
                $old_value ='';
            }

            // skip type NULL with empty fields
            if ($old_value === '' && $new_value === null) {
                continue;
            }

            // attribute not excluded and values are different
            if (!in_array($key, $this->except) && $new_value !== $old_value) {


                $leadTransaction = new LeadTransaction();
                $leadTransaction->lead_id = $lead->id;
                $leadTransaction->attribute = $key;
                $leadTransaction->old_value = $old_value;
                $leadTransaction->new_value = $new_value;
                $leadTransaction->save();


            }
        }
    }

    /**
     * Handle the Lead "deleted" event.
     *
     * @param Lead $lead
     * @return void
     */
    public function deleted(Lead $lead)
    {
        //
    }
}
