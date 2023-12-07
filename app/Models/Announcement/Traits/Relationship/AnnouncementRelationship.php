<?php


namespace App\Models\Announcement\Traits\Relationship;


trait AnnouncementRelationship
{
	public function userAnnouncements()
	{
		return $this->BelongsToMany('App\Models\Auth\User')->select(array('user_id'));
	}
}
