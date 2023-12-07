<?php


namespace App\Http\Controllers\Backend\Payroll;


use App\Http\Controllers\Controller;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\Transaction\LeadTransaction;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class PayrollController extends Controller
{
    protected $leadRepository;

    public function __construct(Lead $lead)
    {
        // set the model
        $this->leadRepository = new LeadRepository();
    }


    public function index()
    {

      return  $this->leadRepository->csvPayRollOutPut();
    }

}
