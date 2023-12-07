import Vue from 'vue'
import VueRouter from 'vue-router'
import * as VueGoogleMaps from 'vue2-google-maps'

// Lead things
import LeadDashboard from '../views/Lead/LeadDashboard'
import LeadCreate from '../views/Lead/LeadCreate'

import LeadAssignAppointment from '../views/Lead/LeadAssignAppointment'
// import AutoComplete from '../components/lead/location/AutoComplete';
import LeadAwait from '../views/Lead/LeadAwait'
import Lead from '../views/Lead/Lead'
import LeadSetAppointment from '../views/Lead/LeadSetAppointment'

// Calendar
import Calender from '../views/Calender/Calender'
import CalendarAppointmentView from '../views/Calender/CalendarAppointmentView'
import TimeOff from '../views/Calender/TimeOff'
import RequestTimeOff from '../views/Calender/RequestTimeOff'
import ApproveTimeOff from '../views/Calender/ApproveTimeOff'

import NotFound from '../components/NotFound'

import LeadQueue from '../views/Queue/LeadQueue'

import Assign from '../views/Queue/Assign'

import ReportingDashboard from '../views/Reporting/ReportingDashboard'
import CommissionDashboard from '../views/Commission/CommissionDashboard'
import History from '../views/Queue/History'
import PayrollShow from '../views/Commission/PayrollShow'
import UserApp from '../views/User/UserApp'
import UserIndex from '../views/User/UserIndex'
import UserShow from '../views/User/UserShow'
import EligibleCity from '../views/Settings/EligibleCity'
import RoundRobinDashboard from '../views/RoundRobin/RoundRobinDashboard'
import LeadClosed from '../views/Lead/LeadClosed'
import Coverage from '../views/RoundRobin/Coverage'
import CallCenterApp from '../views/CallCenter/CallCenter'
import CallCenter from '../views/CallCenter/CallCenter'
import PowerRanking from '../views/Reporting/PowerRanking'
import PostCloseReport from '../views/Calender/PostCloseReport'
import AllChat from '../views/Chat/AllChat'
import Dashboard from '../Dashboard'
import LeadChat from '../views/Chat/LeadChat'
import Teams from '../components/Teams/Office/Teams'
import TeamDashboard from '../views/Office/Team/TeamDashboard'
import KPI from '../views/Reporting/KPI'
import KPIGrouped from '../components/Reporting/KPIGrouped'

// // Lead things
// const LeadDashboard = () => import('../views/Lead/LeadDashboard');
// const LeadCreate = () => import('../views/Lead/LeadCreate');
// const LeadAssignSp1 = () => import('../views/Lead/LeadAssignSp1');
// const LeadAssignAppointment = () => import('../views/Lead/LeadAssignAppointment');
// // const AutoComplete = ( '=> import(../components/lead/location/AutoComplete');
// const LeadAwait = () => import("../views/Lead/LeadAwait");
// const Lead = () => import("../views/Lead/Lead");
// const LeadSetAppointment = () => import("../views/Lead/LeadSetAppointment");
//
// // Calendar
// const Calender = () => import("../views/Calender/Calender");
// const CalendarAppointmentView = () => import("../views/Calender/CalendarAppointmentView");
// const TimeOff = () => import("../views/Calender/TimeOff");
// const RequestTimeOff = () => import("../views/Calender/RequestTimeOff");
// const ApproveTimeOff = () => import("../views/Calender/ApproveTimeOff");
//
// const NotFound = () => import('../components/NotFound');
// const LeadAssignIntegration = () => import("../views/Lead/LeadAssignIntegration");

Vue.use(VueRouter)

const router = new VueRouter({
	mode: 'history',
	linkActiveClass: 'font-bold',
	routes: [
		{
			path: '/dashboard',
			name: 'Dashboard',
			component: Dashboard
		},
		{
			path: '/dashboard/user',
			name: 'User Index',
			component: UserIndex
		},

		{
			path: '/dashboard/user/:userId',
			name: 'User Show',
			component: UserShow
		},

		{
			path: '/dashboard/calendar',
			name: 'Calendar',
			component: Calender
		},
		{
			path: '/dashboard/calendar/post-appointment',
			name: 'Post Appointment',
			component: PostCloseReport
		},
		{
			path: '/dashboard/calendar/:calendarId?',
			name: 'Appointment View',
			component: CalendarAppointmentView
		},
		{
			path: '/dashboard/calendar/:calendarId?/update',
			name: 'Update Appointment',
			component: CalendarAppointmentView
		},
		{
			path: '/dashboard/calendar/time-off',
			name: 'Time Off',
			component: TimeOff
		},
		{
			path: '/dashboard/calendar/time-on/request',
			name: 'Request Time On',
			component: RequestTimeOff
		},

		{
			path: '/dashboard/calendar/time-off/approve',
			name: 'Approve Time off',
			component: ApproveTimeOff
		},
		{
			path: '/dashboard/lead/queue',
			name: 'Queue',
			component: LeadQueue
		},

		{
			path: '/dashboard/lead/',
			name: 'Lead Dashboard',
			component: LeadDashboard
		},
		{
			path: '/dashboard/lead/closed',
			name: 'Lead Closed',
			component: LeadClosed
		},
		{
			name: 'LeadCreate',
			path: '/dashboard/lead/create',
			component: LeadCreate
		},
		{
			path: '/dashboard/lead/await/:queueId?/:leadId?',
			name: 'Lead Await',
			component: LeadAwait
		},
		{
			name: 'Queue History',
			path: '/dashboard/lead/queue/history',
			component: History
		},
		{
			name: 'Queue Assign',
			path: '/dashboard/lead/queue/:queueId?',
			component: Assign
		},

		{
			path: '/dashboard/lead/:leadId?',
			name: 'Lead',
			component: Lead
		},
		{
			path: '/dashboard/lead/:leadId?/set-appointment',
			name: 'Set Appointment',
			component: LeadSetAppointment
		},

		{
			path: '/dashboard/lead/:leadId?/sp2-assign-appointment/:appointmentId?',
			name: 'Set Assign Appointment',
			component: LeadAssignAppointment
		},

		{
			path: '/dashboard/report',
			name: 'reporting dashboard',
			component: ReportingDashboard
		},

		{
			path: '/dashboard/report/power-rank',
			name: 'Power Ranking',
			component: PowerRanking
		},

		{
			path: '/dashboard/commission',
			name: 'Commission Dashboard',
			component: CommissionDashboard
		},

		{
			path: '/dashboard/commission/payroll/:payrollId?',
			name: 'Payroll',
			component: PayrollShow
		},

		{
			path: '/dashboard/settings/eligible-city',
			name: 'Eligible City',
			component: EligibleCity
		},

		{
			path: '/dashboard/round-robin',
			name: 'Round Robin',
			component: RoundRobinDashboard
		},

		{
			path: '/dashboard/round-robin/coverage',
			name: 'Round Robin Coverage',
			component: Coverage
		},

		{
			path: '/dashboard/call-center',
			name: 'Call Center',
			component: CallCenter
		},

		{
			path: '/dashboard/chat',
			name: 'Chat',
			component: LeadChat
		},
		{
			path: '/dashboard/team',
			name: 'creating the best team ever!!!! <3',
			component: TeamDashboard
		},
		{
			path: '/dashboard/report/kpi',
			name: 'KPI',
			component: KPI
		},
		{
			path: '/dashboard/report/kpi/run-chart',
			name: 'Run Chart',
			component: KPIGrouped
		},
        {
          path: '/dashboard/geo-code/:customerId',
            name: 'Geo Code',

            component: () => import('../components/lead/customer/GeoCustomer.vue')
        },
		{
			path: '*',
			component: NotFound
		}
	]
})

export default router
