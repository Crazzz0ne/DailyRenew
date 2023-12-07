<?php


use App\Http\Controllers\Backend\VendorLinks\VendorLinksController;
use App\Http\Controllers\Backend\VendorLinks\LinkLoginController;

Route::middleware(['auth', 'password_expires'])->group(function () {
    Route::namespace('VendorLinks')->prefix('partnerlinks')->name('vendorlinks.')->group(function () {
        Route::get('/', [VendorLinksController::class, 'index'])->name('index');

        Route::post('linklogin/createnew', [LinkLoginController::class, 'store'])->name('linklogin.store');
        Route::post('linklogin/create', [LinkLoginController::class, 'create'])->name('linklogin.create');
        Route::post('linklogin', [LinkLoginController::class, 'update'])->name('linklogin.update');
        Route::get('linklogin/{linklogin}/edit', [LinkLoginController::class, 'edit'])->name('linklogin.edit');
        Route::delete('/linklogin/{linklogin}/delete', [LinkLoginController::class, 'destroy'])->name('linklogin.destroy');

//        Route::resource('linklogin', 'LinkLoginController');



        Route::resource('vendor', 'VendorController');

        Route::resource('link', 'LinkController');

        Route::resource('category', 'CategoryController');
    });
});
