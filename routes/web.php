<?php


use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;

//Route::post('user/login', [\App\Http\Controllers\Api\User\UserController::class, 'login']);
Route::webhooks('webhook/epc/complete', 'complete');
Route::post('webhook/messages/twilio/message', [\App\Http\Controllers\Webhook\Twilio\TwilioController::class, 'message']);
Route::post('webhook/twilio/voice/appointments/welcome', [\App\Http\Controllers\Webhook\Twilio\TwilioController::class, 'welcome']);
Route::post('webhook/twilio/voice/call/customer/{customer}', [\App\Http\Controllers\Webhook\Twilio\TwilioController::class, 'connectWithCustomer']);

Route::webhooks('webhook/tools/sales-rabbit', 'salesRabbit');
Route::webhooks('webhook/customer-upload', 'customerFileUpload');
Route::webhooks('webhook/inbound/bacon', 'baconInbound');
Route::webhooks('webhook/twilio/response', 'TwilioResponse');


/*
 * Global Routes
 * Routes that are used between both frontend and backend.
 */

// Switch between the included languages
Route::get('lang/{lang}', [LanguageController::class, 'swap']);

Route::get('/testmail', [\App\Http\Controllers\TestingController::class, 'sendMail'])->name('sendMail');

Route::get('/testSlack', [\App\Http\Controllers\TestingController::class, 'sendSlack'])->name('sendSlack');


/*
 * Frontend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Frontend', 'as' => 'frontend.'], function () {
    include_route_files(__DIR__.'/frontend/');
});

/*
 * Backend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Backend', 'prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'admin'], function () {
    /*
     * These routes need view-backend permission
     * (good if you want to allow more than one group in the backend,
     * then limit the backend features by different roles or permissions)
     *
     * note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     * These routes can not be hit if the password is expired
     */
    include_route_files(__DIR__.'/backend/');
});




//Route::get('/sendsms', [TwilioSMSController::class, 'sendsms']);

//Route::get('/offline', 'Backend/DashboardController@offline');
