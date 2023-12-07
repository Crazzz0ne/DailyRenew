<?php


namespace App\Repositories\Backend\Admin;


use App\Models\Announcement\Announcement;
use App\Models\Announcement\AnnouncementHasUser;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class AnnouncementRepository extends BaseRepository
{
	/**
	 * Specify Model class name.
	 *
	 * @return mixed
	 */
	public function model()
	{
		return Announcement::class;
	}

	public function getHasUnseenAnyAnnouncement()
	{
		if (Auth::user()) {
			$a = AnnouncementHasUser::where('user_id', '=', Auth::user()->id)
				->count();
			return $a;
		}
	}

	public function stripedHtml($userId, $limit)
	{
		$announcements = $this->badgeAnnouncements($userId, $limit);

		foreach ($announcements as $announcement) {
            if(count($announcement->trixRichText))
			    $announcement->body = str_replace('&nbsp;', '',strip_tags($announcement->trixRichText[0]->content));
		}

		return $announcements;

	}

	public function badgeAnnouncements($userId, $limit)
	{
		$announcements = Announcement::latest('id')->limit($limit)->orderBy('id', 'desc')->get();
		foreach ($announcements as $key => $announcement) {
			$seen               = $this->getHasSeenAnnouncement($userId, $announcement->id);
			$announcement->seen = $seen;

		}
		return $announcements;
	}

	public function getHasSeenAnnouncement($userId, $announcementId)
	{
		$a = AnnouncementHasUser::where([
			['user_id', '=', $userId],
			['announcement_id', '=', $announcementId]
		])->count();
		return $a;
	}


}
