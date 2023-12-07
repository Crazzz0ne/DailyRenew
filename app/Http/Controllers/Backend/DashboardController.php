<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Models\SalesFlow\MassText\MassText;
use App\Repositories\Backend\Admin\AnnouncementRepository;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    private $announcementRepository, $leadRepository;

    public function __construct(AnnouncementRepository $announcementRepository)
    {
        $this->announcementRepository = $announcementRepository;
        $this->leadRepository = new LeadRepository();
    }

    /**
     * @return View
     */
    public function index()
    {


        $announcements = $this->announcementRepository->stripedHtml(Auth::user()->id, 6);
//        return curl -XGET 'http://localhost:8000/export/calendar?name=Hello+World&from=2019-10-10+10%3A10%3A10&to=2019-10-10+11%3A11%3A11&address=Somewhere+In+Siberia&contact_email=rdubrovin@ronasit.com&description=Test+Message'
        return view('backend.dashboard');
    }

    public function theme()
    {
        return view('frontend.theme');
    }

    public function offline()
    {
        return view('vendor/laravelpwa/offline');
    }

}
