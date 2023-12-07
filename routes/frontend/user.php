<?php

use App\Http\Controllers\Frontend\User\AccountController;
use App\Http\Controllers\Frontend\User\DashboardController;
use App\Http\Controllers\Frontend\User\ProfileController;
use App\Http\Controllers\Frontend\Training\TrainingCategoryController;
use App\Http\Controllers\Frontend\Training\TrainingContentController;
use App\Http\Controllers\Frontend\Collateral\CollateralCategoryController;
use App\Http\Controllers\Frontend\Collateral\CollateralContentController;
use App\Http\Controllers\Frontend\Mastermind\MasterMindCategoryController;
use App\Http\Controllers\Frontend\MasterMind\MasterMindContentController;

Route::group(['middleware' => ['auth', 'password_expires']], function () {
    Route::group(['namespace' => 'User', 'as' => 'user.'], function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('account', [AccountController::class, 'index'])->name('account');
        Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});
