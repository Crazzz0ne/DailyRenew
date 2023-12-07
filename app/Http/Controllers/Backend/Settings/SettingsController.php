<?php


namespace App\Http\Controllers\Backend\Settings;


use App\Http\Controllers\Controller;

class SettingsController extends Controller
{

    public function index()
    {
        return view('backend.settings.index');
    }
}
