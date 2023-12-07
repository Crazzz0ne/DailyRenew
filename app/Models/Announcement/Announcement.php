<?php

namespace App\Models\Announcement;

use App\Models\Announcement\Traits\Relationship\AnnouncementRelationship;
use Illuminate\Database\Eloquent\Model;
use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;

class Announcement extends Model
{
	use AnnouncementRelationship;

    use HasTrixRichText;
	public $primaryKey = 'id';

	// Primary Key
	public $timestamps = true;
    protected $guarded = [];

	// Timestamps
	protected $table = 'announcements';
//
//	public   function create($subject, $body, $userId, $color, $sticky)
//	{
//		$this->subject = $subject;
//		$this->body    = $body;
//		$this->user_id = $userId;
//		$this->color   = $color;
//		$this->sticky  = $sticky;
//		$this->save();
//	}
}
