<?php


namespace App\Models;


use Log;

trait LogThings
{
	private function success()
	{
		Log::info('Sucess');
	}

	private function fail()
	{
		Log::warning('Failed');
	}
}
