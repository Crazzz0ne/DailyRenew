<?php


namespace App\Http\Controllers\Api\SalesFlow\Standings;


use App\Http\Controllers\Controller;
use App\Http\Resources\LeadsResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;


class CreditPassController extends Controller
{
    public $leadRepository;

    public function __construct(Lead $lead)
    {
        // set the model
        $this->leadRepository = new LeadRepository();
    }
    public function index(Request $request)
    {
        $startofPayRange = Carbon::now()->hour(1)->minute(00);
        $endOfPayRange = Carbon::now();

        $creditPassLeadIdArray = $this->leadRepository->CreditPassReport($startofPayRange, $endOfPayRange);
      return  $leadResource = LeadsResource::collection(Lead::whereIn('id', $creditPassLeadIdArray)->get());


    }

}
