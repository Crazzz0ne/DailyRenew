<?php


namespace App\Repositories\Backend\Admin;


use App\Models\Training\TrainingContent;

class TrainingContentRepository
{
	/**
	 * Specify Model class name.
	 *
	 * @return mixed
	 */
	public function model()
	{
		return TrainingContent::class;
	}

	public function stripedHtml($contents)
	{
		foreach ($contents as $content) {
			$content->description = strip_tags($content->description);
		}
		return $contents;

	}
}
