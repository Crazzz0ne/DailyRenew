<?php


namespace App\Models\Announcement;


use Illuminate\Database\Eloquent\Model;

class AnnouncementHasUser extends Model
{
	protected $table = 'announcement_user';

    protected $guarded = [];
//	public function create($user_id, $announcement_id)
//	{
//		$this->user_id         = $user_id;
//		$this->announcement_id = $announcement_id;
//		$this->save();
//	}
}
