<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

/**
 * Class LanguageController.
 */
class LanguageController extends Controller
{
	/**
	 * @param $locale
	 *
	 * @return RedirectResponse
	 */
	public function swap($locale)
	{
		if (array_key_exists($locale, config('locale.languages'))) {
			session()->put('locale', $locale);
		}

		return redirect()->back();
	}
}
