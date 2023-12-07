<?php

use App\Http\Controllers;
use App\Http\Controllers\Backend\Printable\PrintableCategoryController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Office\OfficeController;
use App\Http\Controllers\Backend\Annoucement\AnnouncementController;
use App\Http\Controllers\Backend\Training\TrainingCategoryController;
use App\Http\Controllers\Backend\Training\TrainingContentController;
use App\Http\Controllers\Backend\Mastermind\MasterMindCategoryController;
use App\Http\Controllers\Backend\Mastermind\MasterMindContentController;
use App\Http\Controllers\Backend\Office\OfficeStandingController;
use App\Http\Controllers\Backend\SupportController;
use App\Http\Controllers\Backend\Office\ManagerEfficiencyController;


//use App\Http\Controllers\Backend\OfficeController;

// All route names are prefixed with 'dashboard.'.

Route::redirect('/', '/dashboard/lead', 301);
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('instructions', [Controllers\Backend\InstructionController::class, 'index'])->name('instruction');
Route::get('/theme', [DashboardController::class, 'theme'])->name('theme');
Route::namespace('Payroll')->group(function () {
    Route::resource('payroll', 'PayrollController');
});


// Announcements Management
Route::namespace('Office')->group(function () {
    Route::get('officestanding/review/{month}/{year}', [OfficeStandingController::class, 'review'])->name('office.officestandings.review');
    Route::post('officestanding/review', [OfficeStandingController::class, 'update'])->name('office.officestandings.update');
    Route::get('officestanding/{month}/{year}', [OfficeStandingController::class, 'show'])->name('officestanding.show');
//    Route::resource('officestanding', 'OfficeStandingController');
    Route::resource('market', 'Market\MarketController');

//    Route::get('managerefficiency/review', [ManagerEfficiencyController::class, 'review'])->name('office.managerefficiency.review');
//    Route::post('managerefficiency/review', [ManagerEfficiencyController::class, 'update'])->name('office.managerefficiency.review');
//    Route::get('managerefficiency/{month}/{year}', [ManagerEfficiencyController::class, 'show'])->name('managerefficiency.show');
    Route::resource('managerefficiency', 'ManagerEfficiencyController');

    Route::resource('office', 'OfficeController');
});


// Announcements Management
Route::namespace('Announcement')->group(function () {
    Route::resource('announcement', 'AnnouncementController');
});


//Support

//Route::resource('support', 'SupportController');
Route::namespace('Support')->group(function () {

    Route::post('support/create', [SupportController::class, 'store'])->name('support.store');
    Route::post('support/csv', [SupportController::class, 'uploadCSV'])->name('support.csv');
//    Route::get('officestanding/{month}/{year}', [OfficeStandingController::class, 'show'])->name('officestanding.show');
//    Route::resource('officestanding', 'OfficeStandingController');
//    Route::resource('managerefficiency', 'ManagerEfficiencyController');
//    Route::resource('office', 'OfficeController');
});


// training
Route::namespace('Training')->prefix('training')->name('training.')->group(function () {
    Route::get('/', [TrainingCategoryController::class, 'all'])->name('all');
    Route::get('/category', [TrainingCategoryController::class, 'index'])->name('index');
    Route::get('{category}/view', [TrainingCategoryController::class, 'show'])->name('show');
    Route::get('/content/tag', [TrainingContentController::class, 'tag'])->name('content.tag');

    // This is ticky works for now
    Route::namespace('Training')->prefix('category')->group(function () {
        Route::get('/create', [TrainingCategoryController::class, 'create'])->name('category.create');
        Route::post('/', [TrainingCategoryController::class, 'store'])->name('category.store');
        Route::put('/{category}', [TrainingCategoryController::class, 'update'])->name('category.update');
        Route::get('/{category}/edit', [TrainingCategoryController::class, 'edit'])->name('category.edit');

        Route::delete('/{category}', [TrainingCategoryController::class, 'destroy'])->name('category.destroy');
    });

    Route::resource('content', 'TrainingContentController');

//    Route::put('/{content}/update', [TrainingContentController::class, 'update'])->name('category.update');
});

//

// printable
Route::namespace('Printable')->prefix('printable')->name('printable.')->group(function () {
    Route::get('/', [PrintableCategoryController::class, 'all'])->name('all');
    Route::get('/category', [PrintableCategoryController::class, 'index'])->name('index');

    Route::get('{category}/view', [PrintableCategoryController::class, 'show'])->name('show');

    // This is ticky works for now
    Route::namespace('Printable')->prefix('category')->group(function () {
        Route::get('/create', [PrintableCategoryController::class, 'create'])->name('category.create');
        Route::post('/', [PrintableCategoryController::class, 'store'])->name('category.store');
        Route::put('/{category}/update', [PrintableCategoryController::class, 'update'])->name('category.update');

        Route::get('/{category}/edit', [PrintableCategoryController::class, 'edit'])->name('category.edit');

        Route::delete('/{category}/delete', [TrainingCategoryController::class, 'destroy'])->name('category.destroy');
    });

    Route::resource('content', 'PrintableContentController');

//    Route::put('/{content}/update', [CollateralContentController::class, 'update'])->name('category.update');
});


// mastermind
Route::namespace('Mastermind')->prefix('mastermind')->name('mastermind.')->group(function () {
    Route::get('/', [MasterMindCategoryController::class, 'all'])->name('all');
    Route::get('/category', [MasterMindCategoryController::class, 'index'])->name('index');
    Route::get('{category}/view', [MasterMindCategoryController::class, 'show'])->name('show');
    Route::get('/tag', [MasterMindContentController::class, 'tag'])->name('content.tag');


    Route::namespace('Mastermind')->prefix('category')->group(function () {
        Route::get('/create', [MasterMindCategoryController::class, 'create'])->name('category.create');
        Route::post('/', [MasterMindCategoryController::class, 'store'])->name('category.store');
        Route::put('/{category}', [MasterMindCategoryController::class, 'update'])->name('category.update');
        Route::get('/{category}/edit', [MasterMindCategoryController::class, 'edit'])->name('category.edit');

        Route::delete('/{category}', [MasterMindCategoryController::class, 'destroy'])->name('category.destroy');
    });

    Route::resource('content', 'MasterMindContentController');

//    Route::put('/{content}/update', [TrainingContentController::class, 'update'])->name('category.update');

    // Announcements Management

});
//USER
Route::namespace('SalesFlow')->prefix('leads')->name('salesFlow.')->group(function () {
    Route::namespace('Appointment')->group(function () {
        Route::resource('appointment', 'AppointmentController');
    });
});

Route::group(['namespace' => 'User', 'as' => 'user.'], function () {

    Route::get('account', [Controllers\Backend\User\AccountController::class, 'index'])->name('account');
    Route::put('profile/update', [Controllers\Backend\User\ProfileController::class, 'update'])->name('profile.update');
    Route::post('text-test', [Controllers\Backend\User\ProfileController::class, 'testText'])->name('test-text');

    Route::group(['middleware' => 'password_expires'], function () {
        // Change Password Routes
        Route::patch('password/update', [Controllers\Backend\User\UpdatePasswordController::class, 'update'])->name('password.update');
    });
});
Route::get('masstext/', [Controllers\Backend\SalesFlow\MassTextMessage\MassTextMessageController::class, 'index'])->name('masstext.index');
Route::get('masstext/create', [Controllers\Backend\SalesFlow\MassTextMessage\MassTextMessageController::class, 'create'])->name('masstext.create');
Route::post('masstext/create', [Controllers\Backend\SalesFlow\MassTextMessage\MassTextMessageController::class, 'store'])->name('masstext.store');
Route::get('lead/{catchall?}', [Controllers\Backend\SalesFlow\Lead\LeadController::class, 'index'])->where('catchall', '(.*)')->name('lead');
Route::get('calendar/{catchall?}', [Controllers\Backend\SalesFlow\Calender\CalenderController::class, 'index'])->where('catchall', '(.*)')->name('calender');
Route::get('report/{catchall?}', [Controllers\Api\SalesFlow\Reporting\ReportController::class, 'index'])->where('catchall', '(.*)')->name('report');
Route::get('commission/{catchall?}', [Controllers\Api\Commission\CommissionController::class, 'base'])->where('catchall', '(.*)')->name('commission');
Route::get('unsignedRep', [Controllers\Backend\SalesFlow\Calender\CalenderController::class, 'unsignedRep'])->name('unsignedRep');
Route::get('user/{catchall?}', [Controllers\Backend\SalesFlow\User\UserController::class, 'index'])->where('catchall', '(.*)')->name('user');
Route::get('queue/{catchall?}', [Controllers\Backend\Line\LineController::class, 'index'])->where('catchall', '(.*)')->name('line');
Route::get('settings/{catchall?}', [Controllers\Backend\Settings\SettingsController::class, 'index'])->where('catchall', '(.*)')->name('settings');
Route::get('round-robin/{catchall?}', [Controllers\Backend\RoundRobin\RoundRobinController::class, 'index'])->where('catchall', '(.*)')->name('roundRobin');
Route::get('call-center/{catchall?}', [Controllers\Backend\RoundRobin\RoundRobinController::class, 'index'])->where('catchall', '(.*)')->name('call-center');
Route::get('team/{catchall?}', [Controllers\Backend\RoundRobin\RoundRobinController::class, 'index'])->where('catchall', '(.*)')->name('team');
Route::get('geo-code/{catchall?}', [Controllers\Backend\SalesFlow\Lead\LeadController::class, 'index'])->where('catchall', '(.*)')->name('teams');

Route::get('chat/{catchall?}', [Controllers\Backend\RoundRobin\RoundRobinController::class, 'index'])->where('catchall', '(.*)')->name('chat');
//Route::get('test', function (){
//   return view('listenBroadcast');
//});
