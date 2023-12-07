<?php

use App\Http\Controllers\Api\Notifications\NotificationController;
use App\Http\Controllers\Api\SalesFlow\Customer\CustomerRoofController;
use App\Http\Controllers\Api\SalesFlow\Lead\SiteSurveyQuestionController;
use App\Http\Controllers\Backend\VendorLinks\VendorController;
use \App\Http\Controllers\Api\Office\OfficeStandingsController;
use App\Http\Controllers\Api\SalesFlow\Lead\LeadController;
use \App\Http\Controllers\Api\SalesFlow\LocationServices\LocationController;
use \App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\SalesFlow\Lead\UploadController;
use App\Http\Controllers\Api\SalesFlow\Lead\Utility\UtilityUsageController;
use App\Http\Controllers\Api\SalesFlow\Lead\AppointmentController;
use App\Http\Controllers\Api\SalesFlow\Customer\CustomerController;
use App\Http\Controllers\Api\User\UserAvailabilityController;
use App\Http\Controllers\Api\Settings\EligibleCityController;
use App\Http\OnboardUserTransfer;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/import-from-geo', [\App\Http\Controllers\Api\External\GeoCode\CreateLeadController::class, 'store']);
//Route::post('/get-in-now', [OnboardUserTransfer::class, 'bringInUser']);
//Route::post('/get-leads-in-now', [OnboardUserTransfer::class, 'bringInLead']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('/location-geo', [\App\Http\Controllers\Api\Micro\GeoLocationController::class, 'index'])->name('location-geo.index');


    Route::post('/testing', [\App\Http\Controllers\Api\TestingController::class, 'index']);
    Route::post('/all-notes', [\App\Http\Controllers\Api\SalesFlow\Lead\NoteController::class, 'showAll'])->name('allnotes');
    Route::post('/all-notes', [\App\Http\Controllers\Api\SalesFlow\Lead\NoteController::class, 'showAll'])->name('allnotes');
    Route::post('/notes/lead/{lead}/latest', [\App\Http\Controllers\Api\SalesFlow\Lead\NoteController::class, 'latest'])->name('lead.notes.latest');

    Route::get('/commission', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'index']);
    Route::get('/commission/types', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'commissionTypes']);
    Route::post('/commission', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'store']);
    Route::put('/commission/{commission}', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'update']);
    Route::post('/commission/{commissionLedgers}/approve', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'approve']);
    Route::delete('/commission/{commissionLedgers}', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'destroy']);

    Route::get('/commission/total/rep/{user}', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'repTotal']);
    Route::get('/commission/transaction/rep/{user}', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'repTransaction']);
    Route::get('/commission/transaction', [\App\Http\Controllers\Api\Commission\CommissionController::class, 'officeTransaction']);
    Route::get('/payroll/{payroll}', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'show'])->name('payroll.show');
    Route::post('commissions/payroll-upload', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'upload'])->name('payroll.upload');
    Route::get('/payroll/office/{office}', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'office']);
    Route::get('/payroll/rep/{user}', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'user']);
//    Route::get('/payroll/rep/{user}/preview', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'previewUserPayroll']);
    Route::post('/payroll/previews', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'preview']);
    Route::get('/payrolls/audits/customer/{lead}/credit', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'creditPassAudit']);
    Route::get('/payrolls/audits/customer/{customer}/cell-phone', [\App\Http\Controllers\Api\Payroll\PayrollController::class, 'cellPhoneAudit']);
    Route::post('/customer/{customer}/geo', [CustomerController::class, 'geoLocation'])->name('customer.geo');
    Route::get('/vendors', [VendorController::class, 'all'])->name('vendors.all');

    Route::post('/round-robin/go-back-toggle', [\App\Http\Controllers\Api\RoundRobin\RoundRobinController::class, 'inGoBack']);
    Route::get('/round-robin/coverage', [\App\Http\Controllers\Api\RoundRobin\RoundRobinAvailabilityController::class, 'index']);
    Route::get('/round-robin/cities', [\App\Http\Controllers\Api\RoundRobin\RoundRobinAvailabilityController::class, 'byCities']);

    Route::resource('officestandings', '\App\Http\Controllers\Api\Office\OfficeStandingsController');

    Route::get('office/{office}/team', [\App\Http\Controllers\Api\Office\TeamController::class, 'index']);
    Route::get('office/{office}/team/{team}', [\App\Http\Controllers\Api\Office\TeamController::class, 'show']);
    Route::post('office/{office}/team', [\App\Http\Controllers\Api\Office\TeamController::class, 'store']);
    Route::post('office/{office}/team/{team}/users', [\App\Http\Controllers\Api\Office\TeamController::class, 'addTeamMember']);
    Route::put('office/{office}/team/{team}', [\App\Http\Controllers\Api\Office\TeamController::class, 'update']);
    Route::put('office/{office}/team/{team}/team-captain/{user}', [\App\Http\Controllers\Api\Office\TeamController::class, 'addTeamCaptain']);
    Route::delete('office/{office}/team/{team}', [\App\Http\Controllers\Api\Office\TeamController::class, 'delete']);
    Route::delete('office/{office}/team/{team}/user/{user}', [\App\Http\Controllers\Api\Office\TeamController::class, 'removeTeamMember']);
    Route::delete('office/{office}/team/{team}/user/{user}', [\App\Http\Controllers\Api\Office\TeamController::class, 'removeTeamCaptain']);

    Route::get('officestandings/{month}', [OfficeStandingsController::class, 'showMonth']);

    Route::get('region', [\App\Http\Controllers\Api\Office\RegionController::class, 'index']);
    Route::get('region/{market}/users', [\App\Http\Controllers\Api\Office\RegionController::class, 'users']);

    Route::group(['namespace' => 'Api\Office', 'prefix' => 'office', 'as' => 'office.'], function () {
        Route::resource('powerCompany', 'Market\PowerCompanyController');
        Route::resource('{office}/user', 'OfficeUsersController');
        Route::get('/', [\App\Http\Controllers\Api\Office\OfficeController::class, 'index']);
        Route::get('/{office}/role', [\App\Http\Controllers\Api\Office\OfficeController::class, 'role']);
        Route::get('/all-eligible-city-tags', [\App\Http\Controllers\Api\Office\OfficeEligibleCityController::class, 'masterList']);
        Route::get('/{office}/eligible-city-tags', [\App\Http\Controllers\Api\Office\OfficeEligibleCityController::class, 'index']);
        Route::post('/{office}/eligible-city-tags', [\App\Http\Controllers\Api\Office\OfficeEligibleCityController::class, 'store']);
        Route::get('/{office}/city-list-for-user-r-r', [\App\Http\Controllers\Api\RoundRobin\OfficeRoundRobinController::class, 'cityListForUserRR']);
        Route::post('/{office}/round-robin', [\App\Http\Controllers\Api\RoundRobin\OfficeRoundRobinController::class, 'store']);
        Route::delete('/{office}/round-robin', [\App\Http\Controllers\Api\RoundRobin\OfficeRoundRobinController::class, 'delete']);
    });
    Route::get('epc/jwt/{lead}', [\App\Http\Controllers\Api\Epc\EpcController::class, 'jwt']);
    Route::get('epc/lead/{lead}/self-survey', [\App\Http\Controllers\Api\Epc\EpcController::class, 'selfSiteSurvey']);
    Route::resource('epc', 'Api\Epc\EpcController');
    Route::group(['namespace' => 'Api\Epc', 'prefix' => 'epc', 'as' => 'epc.'], function () {
        Route::resource('{epc}/adders', 'EpcAddersController');
    });

    Route::resource('/user', 'Api\User\UserController');
    Route::post('/user/{user}/batch-city', [UserController::class, 'batchCity']);
    Route::get('/user/{user}/get-selected-cities', [UserController::class, 'selectedCities']);
    Route::post('/user/{user}/eligible-city-tags', [UserController::class, 'storeSelectedCities']);
    Route::post('/user/{user}/terminate', [UserController::class, 'terminate']);
    Route::post('/user/{user}/roles', [UserController::class, 'updateRoles']);
    Route::post('/user/{user}/updateLanguages', [UserController::class, 'updateLanguages']);
    Route::post('/user/{user}/update-remote', [UserController::class, 'updateRemote']);
    Route::post('/user/{user}/payscale', [UserController::class, 'updatePayscale']);
    Route::post('/user/{user}/timezone', [UserController::class, 'updateTimezone']);
    Route::post('/user/{user}/update-auto-rr', [UserController::class, 'updatAutoRR']);
    Route::put('/user/{user}/office', [UserController::class, 'updateOffice']);

    Route::namespace('Settings')->prefix('settings')->name('settings.')->group(function () {
        Route::prefix('eligible-city')->name('eligibleCity.')->group(function () {
            Route::get('/', [EligibleCityController::class, 'index'])->name('index');
            Route::post('/upload', [EligibleCityController::class, 'upload'])->name('upload');
            Route::get('/{office}', [EligibleCityController::class, 'show'])->name('office');
            Route::post('/', [EligibleCityController::class, 'store'])->name('store');
            Route::delete('/', [EligibleCityController::class, 'delete'])->name('delete');

        });
    });

    Route::get('notes/leads', [\App\Http\Controllers\Api\SalesFlow\Lead\NoteController::class, 'leads'])->name('notes.leads');
    Route::get('notes/lead/{lead}', [\App\Http\Controllers\Api\SalesFlow\Lead\NoteController::class, 'leadMessage'])->name('notes.lead');
    Route::post('reporting/kpi', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'kpi'])->name('reporting.kpi');
//    Route::post('reporting/run-chart', [\App\Http\Controllers\Api\SalesFlow\Reporting\KPIRunChartController::class, 'index'])->name('reporting.runChart');
    Route::namespace('Api\SalesFlow')->prefix('salesflow')->name('salesFlow.')->group(function () {

        Route::group(['namespace' => 'Api\SalesFlow\Reporting', 'prefix' => 'reporting', 'as' => 'reporting.'], function () {
            Route::get('/pie-ratio', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'allJobsPie']);
            Route::post('/closed-ratio', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'closeRatio']);
            Route::get('/count-position', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'assignedLeads']);
            Route::get('/credit-pass-appointments', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'creditPassWithAppointment']);
            Route::post('/sit-ratio', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'sitRatio']);
            Route::post('/sit-ratio-detailed', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'sitRatioDetailed']);
            Route::get('sit-by-user', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'sitByUser']);
            Route::get('start-to-close', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'startToClose']);
            Route::get('closed-install-ratio', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'closedRatio']);
            Route::get('sit-ratio-office', [\App\Http\Controllers\Api\SalesFlow\Reporting\ReportController::class, 'sitRatioOffice']);
            Route::get('power-ranking', [\App\Http\Controllers\Api\SalesFlow\Reporting\PowerRankingController::class, 'index']);


//            sit-ratio-office
        });
        Route::get('/line/time-to-fill', [\App\Http\Controllers\Api\SalesFlow\Queue\LineController::class, 'timeToFill']);

        Route::get('/line/history', [\App\Http\Controllers\Api\SalesFlow\Queue\LineController::class, 'history'])->name('line.history');
        Route::get('/line/count', [\App\Http\Controllers\Api\SalesFlow\Queue\LineController::class, 'queueCounts']);
        Route::get('/line/lead/{lead}', [\App\Http\Controllers\Api\SalesFlow\Queue\LineController::class, 'leadLine']);

        Route::post('/line/go-back', [\App\Http\Controllers\Api\SalesFlow\Queue\LineController::class, 'setGoBack']);
        Route::resource('/line', 'Queue\LineController');
        Route::get('/line/{line}/others', [\App\Http\Controllers\Backend\Line\LineController::class, 'others']);

        Route::namespace('line')->prefix('line')->group(function () {
            Route::get('{line}/position', [\App\Http\Controllers\Api\SalesFlow\Queue\LineController::class, 'position']);
        });
        Route::post('customer/{customer}/call', [\App\Http\Controllers\Webhook\Twilio\TwilioController::class, 'makeCall']);
        Route::namespace('Standings')->prefix('standings')->group(function () {
            Route::resource('credit-pass', 'CreditPassController');
        });
        Route::get('customer/{customer}/customer-message', [\App\Http\Controllers\Api\SalesFlow\Customer\CustomerMessageController::class, 'index']);
        Route::post('customer/{customer}/customer-message', [\App\Http\Controllers\Api\SalesFlow\Customer\CustomerMessageController::class, 'store']);
        Route::resource('customer', 'Customer\CustomerController');
        Route::get('customer/multi-lead/{customer}', [CustomerController::class, 'multiLead']);
        Route::get('/', [LocationController::class, 'getLocation'])->name('getLocation');
        Route::group(['namespace' => 'Calender'], function () {
            Route::post('/post-appointment', [\App\Http\Controllers\Api\SalesFlow\Calender\PostAppointmentController::class, 'index'])->name('calender.post-appointment');
            Route::resource('calender', 'CalenderController');
            Route::get('/show-by-lead-id/{id}', [\App\Http\Controllers\Api\SalesFlow\Calender\CalenderController::class, 'showByLeadId'])->name('calender.leadid');
            Route::resource('/availability', 'AvailabilityController');
        });
        Route::get('lead/status', [LeadController::class, 'status']);
//        updateLeadStatus
        Route::get('lead/closed', [LeadController::class, 'closed']);
        Route::get('lead/closed-count', [LeadController::class, 'closedCount']);
        Route::post('lead-dashboard', [LeadController::class, 'index']);

        Route::resource('lead', 'Lead\LeadController')->middleware(\App\Http\Middleware\ConvertStringBooleansToBooleans::class);
        Route::group(['namespace' => 'Lead', 'prefix' => 'lead', 'as' => 'lead.'], function () {
            Route::get('/roof/types', [CustomerRoofController::class, 'index']);
            Route::post('{lead}/roof/delete', [CustomerRoofController::class, 'delete']);
            Route::post('{lead}/roof', [CustomerRoofController::class, 'store']);
            Route::post('{lead}/update-status', [LeadController::class, 'updateLeadStatus']);

            Route::post('{lead}/system', [\App\Http\Controllers\Api\SalesFlow\Lead\SystemController::class, 'closeIt']);
            Route::get('{lead}/sales-packet', [\App\Http\Controllers\Api\SalesFlow\Lead\SalesPacketController::class, 'index']);
            Route::get('{lead}/sales-packet/{id}', [\App\Http\Controllers\Api\SalesFlow\Lead\SalesPacketController::class, 'show']);
            Route::put('{lead}/sales-packet/{id}', [\App\Http\Controllers\Api\SalesFlow\Lead\SalesPacketController::class, 'update']);
            Route::post('{lead}/sales-packet/{id}/create-change-order', [\App\Http\Controllers\Api\SalesFlow\Lead\SalesPacketController::class, 'createChangeOrder']);
            Route::post('{lead}/sales-packet/{id}/cancel-change-order', [\App\Http\Controllers\Api\SalesFlow\Lead\SalesPacketController::class, 'cancelChangeOrder']);

            Route::get('{lead}/proposed-system', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'index']);
            Route::get('{lead}/proposed-system/{proposedSystem}', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'show']);
            Route::post('{lead}/proposed-system', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'create']);
            Route::put('{lead}/proposed-system/{proposedSystem}', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'update']);
            Route::post('{lead}/proposed-system/{ProposedSystem}/submit-to-rep', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'submitToRep']);
            Route::post('{lead}/proposed-system/{ProposedSystem}/rep-approved', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'repApproved']);
            Route::post('{lead}/proposed-system/{ProposedSystem}/rep-approved-ss', [\App\Http\Controllers\Api\SalesFlow\Lead\SolvingSolarLeadSubmitController::class, 'post']);
            Route::post('{lead}/proposed-system/{ProposedSystem}/rep-approved-change-order', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'repApprovedChangeOrder']);
            Route::post('{lead}/proposed-system/{proposedSystem}/approve', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'approve']);
            Route::post('{lead}/proposed-system/upload', [\App\Http\Controllers\Api\SalesFlow\Lead\ProposedSystemController::class, 'upload']);

            Route::get('{lead}/requested-system', [\App\Http\Controllers\Api\SalesFlow\Lead\RequestedSystemController::class, 'index']);
            Route::post('{lead}/requested-system', [\App\Http\Controllers\Api\SalesFlow\Lead\RequestedSystemController::class, 'create']);
            Route::put('{lead}/requested-system/{requestedSystem}', [\App\Http\Controllers\Api\SalesFlow\Lead\RequestedSystemController::class, 'update']);
            Route::get('{lead}/requested-system/{requestedSystem}', [\App\Http\Controllers\Api\SalesFlow\Lead\RequestedSystemController::class, 'show']);
            Route::post('{lead}/requested-system/{requestedSystem}/request', [\App\Http\Controllers\Api\SalesFlow\Lead\RequestedSystemController::class, 'request']);
            Route::post('{lead}/requested-system/{requestedSystem}/change-order', [\App\Http\Controllers\Api\SalesFlow\Lead\RequestedSystemController::class, 'changeOrder']);
//            Route::put('{lead}/status', [LeadController::class, 'status']);
            Route::get('{lead}/system', [\App\Http\Controllers\Api\SalesFlow\Lead\SystemController::class, 'index']);
            Route::put('{lead}/system/{system}', [\App\Http\Controllers\Api\SalesFlow\Lead\SystemController::class, 'update']);
            Route::resource('{lead}/proposal-builder', 'ProposalBuilderController');
            Route::post('{lead}/jnj', 'LeadController@jeopardy');
            Route::get('{lead}/appointment/available', [AppointmentController::class, 'AllAvailableAppointments']);
            Route::get('{lead}/appointment/available-office', [AppointmentController::class, 'bookD2dRR']);
            Route::post('{lead}/appointment/available', [AppointmentController::class, 'bookRR']);
            Route::resource('{lead}/appointment', 'AppointmentController');
            Route::put('{lead}/appointment/{appointment}/assignSp2', 'AppointmentController@assignSp2')->name('appointment.assignSp2');
            Route::get('{lead}/appointment/{appointment}/change-rep', [AppointmentController::class, 'reassignList']);
            Route::put('{lead}/appointment/{appointment}/update-remote', [AppointmentController::class, 'updateRemote']);
            Route::get('{lead}/appointment/{appointment}/re-book', [AppointmentController::class, 'reBook']);
            Route::put('{lead}/appointment/{appointment}/re-book', [AppointmentController::class, 'updateRebook']);
            Route::post('{lead}/appointment/{appointment}/complete', [AppointmentController::class, 'updateCompleted']);
            Route::post('{lead}/appointment/book-event', [AppointmentController::class, 'bookEvent']);
            Route::put('{lead}/appointment/{appointment}/update-status', [AppointmentController::class, 'updateStatus']);

            Route::resource('{lead}/note', 'NoteController');
            Route::resource('{lead}/upload', 'UploadController');
            Route::post('{lead}/upload/download-all', [UploadController::class, 'downloadAll']);

            Route::resource('{lead}/rep', 'RepsController');
//            Route::resource('{lead}/proposal', 'ProposalController');
            Route::resource('{lead}/utility', 'LeadUtilityController');
            Route::resource('{lead}/utility/{utility}/usage', 'Utility\UtilityUsageController');
            Route::resource('{lead}/utility/{utility}/password', 'LeadLoginController');
        });


        Route::namespace('User')->prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'getUser'])->name('index');
            Route::get('/event-list', [UserController::class, 'eventList'])->name('eventList');

            Route::get('/api', [UserController::class, 'getUser'])->name('getUser');
            Route::get('/office', [UserController::class, 'office'])->name('office');
            Route::post('/go-back-toggle', [UserController::class, 'goBackAvailable']);
            Route::get('/position', [UserController::class, 'byPosition']);
            Route::post('{user}/availability', [UserAvailabilityController::class, 'store']);
            Route::get('{user}/availability', [UserAvailabilityController::class, 'index']);
            Route::put('{user}/availability', [UserAvailabilityController::class, 'update']);
            Route::delete('{user}/availability/{availability}', [UserAvailabilityController::class, 'delete']);

        });
    });
    Route::post('/user/count', [UserController::class, 'count'])->name('user.count');
    Route::namespace('RoundRobin')->prefix('round-robin')->name('RoundRobin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\RoundRobin\RoundRobinController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Api\RoundRobin\RoundRobinController::class, 'store'])->name('store');
    });

    Route::namespace('Notification')->prefix('notifications')->name('Notification.')->group(function () {
        Route::get('{user}/count', [NotificationController::class, 'count'])->name('notification-count');
        Route::post('{user}/show-all', [NotificationController::class, 'showAll'])->name('notification-show-all');
        Route::post('{notification}/read', [NotificationController::class, 'read'])->name('notification-mark-as-read');
        Route::post('{user}/read-all', [NotificationController::class, 'readAll'])->name('mark-all-read');
        Route::post('{notification}/unread', [NotificationController::class, 'unread'])->name('notification-mark-as-unread');
        Route::post('{user}/unread-all', [NotificationController::class, 'unreadAll'])->name('mark-all-unread');
        Route::delete('{notification}/delete', [NotificationController::class, 'delete'])->name('notification-delete');
        Route::delete('{user}/delete-all', [NotificationController::class, 'deleteAll'])->name('notification-delete-all');
    });

    Route::put('site-survey-question/{completeSiteSurveyQuestions}', [SiteSurveyQuestionController::class, 'update']);
    Route::post('/call-center/rehash-files', [\App\Http\Controllers\Api\CallCenter\RehashFilesController::class, 'store']);
});



