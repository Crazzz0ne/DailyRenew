<?php

namespace App\Http\Composers\Backend;

use App\Repositories\Backend\Admin\AnnouncementRepository;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\View\View;

/**
 * Class SidebarComposer.
 */
class SidebarComposer
{
	/**
	 * @var UserRepository
	 */
	protected $userRepository, $announcementRepository;

	/**
	 * SidebarComposer constructor.
	 *
	 * @param UserRepository $userRepository
	 * @param AnnouncementRepository $announcementRepository
	 */
	public function __construct(UserRepository $userRepository, AnnouncementRepository $announcementRepository)
	{
		$this->userRepository         = $userRepository;
		$this->announcementRepository = $announcementRepository;
	}

	/**
	 * @param View $view
	 *
	 * @return bool|mixed
	 */
	public function compose(View $view)
	{
//        dd(auth()->user()->roles );
//		if (config('access.users.requires_approval')) {
//			if (auth()->user()->roles[0]->name == 'manager') {
//				$view->with('pending_approval', $this->userRepository->getUnconfirmedCountManager());
//			} else {
//				$view->with('pending_approval', $this->userRepository->getUnconfirmedCount());
//			}
//
//		} else {
//			$view->with('pending_approval', 0);
//		}

		$view->with('unread_count', $this->announcementRepository->getHasUnseenAnyAnnouncement());
	}
}
