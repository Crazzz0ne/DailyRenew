<?php


namespace App\Http\Composers\Backend;

use App\Repositories\Backend\SalesFlow\Lead\AppointmentRepository;
use Illuminate\View\View;

class HeaderComposer
{
    /**
     * @var AppointmentRepository
     */
    protected $appointmentRepository;

    /**
     * SidebarComposer constructor.
     *
     * @param AppointmentRepository $appointmentRepository
     *
     */
    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;;
    }

    /**
     * @param View $view
     *
     * @return bool|mixed
     */
    public function compose(View $view)
    {
//        dd(auth()->user()->isAdmin() );
//        TODO:set this to work with offices
        if (auth()->user()->hasAnyRole('manager', 'executive', 'administrator')) {
            $view->with('unAssignedAppointment', $this->appointmentRepository->unAssignedAppointment());
            $view->with('unAssignedAppointmentList', $this->appointmentRepository->unAssignedAppointmentList());
        } else {
            $view->with('unAssignedAppointment', 0);
        }

    }
}
