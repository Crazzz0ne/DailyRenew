<?php

namespace App\Http\Composers\Frontend;

use App\Repositories\Backend\Admin\AnnouncementRepository;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\View\View;

/**
 * Class NavbarComposer.
 */
class NavbarComposer
{
	/**
	 * @var UserRepository
	 */
	protected $announcementRepository;

	/**
	 * SidebarComposer constructor.
	 *
	 * @param AnnouncementRepository $announcementRepository
	 */
	public function __construct(AnnouncementRepository $announcementRepository)
	{

		$this->announcementRepository = $announcementRepository;
	}

	/**
	 * @param View $view
	 *
	 * @return bool|mixed
	 */
	public function compose(View $view)
	{
		$view->with('unread_count', $this->announcementRepository->getHasUnseenAnyAnnouncement());
	}
}
