<template>
  <div
    v-if="$can('view lead')"
    class="container-fluid"
  >
    <div class="row justify-content-between  pt-3">
      <div class="col-md-3 col-sm-12">
        <template>
          <UserOfficeSelect
            :can-view-regions=" $can('accept proposal builder') || $can('administrate company')"
            :can-view-offices="$can('view office') || $can('accept proposal builder') || $can('administrate company')"
            :can-view-users="$can('view office') || $can('accept proposal builder') || $can('administrate company')"
            :user-market-id="14"
            @officeChange="officeChanged($event)"
            @userChange="userChanged($event)"
            @selectedRegionId="regionChanged($event)"
          />
        </template>
      </div>
      <!--      <div class="col-md-3 col-sm-12">-->
      <!--        <template v-if="$can('administrate company') || $can('accept proposal builder')">-->
      <!--          <MazSelect-->
      <!--            v-model="leadSourceFilter"-->
      <!--            :options="sourceOptions"-->
      <!--            :placeholder="'Source'"-->
      <!--            :size="'sm'"-->
      <!--            @input="sourceChanged()"-->
      <!--          />-->
      <!--        </template>-->
      <!--      </div>-->

      <div class="col-md-3 col-sm-12">
        <template>
          <MazSelect
            v-model="typeFilter"
            :options="typeOptions"
            :placeholder="'Type'"
            :size="'sm'"
            @input="changeType()"
          />
        </template>
      </div>
    </div>

    <div class="row justify-content-between pt-4">
      <div class="col-md-3">
        <router-link
          :to="{ name:'Post Appointment' }"
          data-toggle="tooltip"
          title=""
          class="btn btn-primary ml-1"
        >
          <span class="pr-1">Post Close Report </span>
          <i class="fas fa-plus-circle" />
        </router-link>
      </div>
      <div class="col-md-3">
        <router-link
          :to="{ name:'Request Time On' }"
          data-toggle="tooltip"
          title=""
          class="btn btn-primary ml-1"
        >
          <span class="pr-1">Manage Availability </span>
          <i class="fas fa-plus-circle" />
        </router-link>
      </div>
    </div>
    <div class="demo-app">
      <div class="demo-app-top">
        <!--            <button @click="requestTimeOff" class="fc-today-button fc-button fc-button-primary text-capitalize">Request-->
        <!--                Time Off-->
        <!--            </button>-->
        <!--            <button @click="approveTimeOff" class="fc-today-button fc-button fc-button-primary text-capitalize">Approve-->
        <!--                Time Off-->
        <!--            </button>-->
      </div>
      <FullCalendar
        ref="fullCalendar"
        class="demo-app-calendar p-4"
        default-view="dayGridMonth"

        :header="{
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        }"
        :plugins="calendarPlugins"
        :weekends="calendarWeekends"
        :events="calendarEvents"
        @dateClick="handleDateClick"
        @datesRender="getAppointmentsIfNew"
      />
    </div>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'
import UserOfficeSelect from '../../components/Selects/UserOfficeSelect'
import RequestTimeOff from './RequestTimeOff'

export default {
	// eslint-disable-next-line vue/multi-word-component-names
	name: 'Calendar',
	components: {
		// eslint-disable-next-line vue/no-unused-components
		RequestTimeOff,
		UserOfficeSelect,
		FullCalendar // make the <FullCalendar> tag available
	},
	data: function () {
		return {
			leadSourceFilter: 'all',
			sourceOptions: [
				{ label: 'All', value: 'all' },
				{ label: 'Call Center', value: 'call-center' },
				{ label: 'In-house', value: 'in-house' }
			],
			typeFilter: 6,
			typeOptions: [
				{ label: 'All', value: null },
				{ label: 'Follow Up', value: 7 },
				{ label: 'Closes', value: 6 },
				{ label: 'Site Surveys', value: 4 },
				{ label: 'Installs', value: 5 }
			],

			calendarPlugins: [ // plugins must be defined in the JS
				dayGridPlugin,
				timeGridPlugin
				// needed for dateClick
			],
			calendarWeekends: true,
			calendarEventsPull: {},
			calendarEvents: [ // initial event data
				{ title: 'Loading', start: new Date() }
			],
			permissions: window.Permissions,
			type: 'user',
			selectedOffice: null,
			selectedUser: null,
			selectedRegion: null,
			draw: 0
		}
	},
	created () {
		this.setTypeFromPermission()
	},
	mounted () {
		if (this.$can('administrate company') || this.$can('accept proposal builder')) {
			this.appointmentList()
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		])
		// options () {
		// 	if (this.$can('administrate company') || this.$can('accept proposal builder') || this.$can('accept credit runner')) {
		// 		return [
		// 			{ label: 'Personal', value: 'user' },
		// 			{ label: 'Office', value: 'office' },
		// 			{ label: 'Company', value: 'company' }
		//
		// 		]
		// 	} else {
		// 		return [
		// 			{ label: 'Personal', value: 'user' },
		// 			{ label: 'Office', value: 'office' }
		// 		]
		// 	}
		// }
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		setTypeFromPermission () {
			if (this.$can('administrate company') || this.$can('accept proposal builder')) {
				this.typeFilter = 6
			} else {
				this.typeFilter = null
			}
		},
		getAppointmentsIfNew () {
			if (this.draw > 0) {
				this.appointmentList()
			}
		},

		handleDateClick (arg) {
			alert('date click! ' + arg.dateStr)
		},
		userChanged (event) {
			this.selectedUser = event
			this.appointmentList()
		},
		officeChanged (event) {
			this.selectedOffice = event
			this.appointmentList()
		},
		regionChanged (event) {
			this.selectedRegion = event
			this.appointmentList()
		},
		sourceChanged () {
			this.appointmentList()
		},
		changeType () {
			this.appointmentList()
		},

		appointmentList () {
			this.draw++
			const calendarApi = this.$refs.fullCalendar.getApi()

			axios.post('/api/salesflow/calender', {
				source: this.leadSourceFilter,
				selectedOffice: this.selectedOffice,
				selectedUser: this.selectedUser,
				selectedRegion: this.selectedRegion,
				type: this.typeFilter,
				start: calendarApi.state.dateProfile.activeRange.start,
				end: calendarApi.state.dateProfile.activeRange.end
			})
				.then((response) => {
					this.calendarEvents = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		requestTimeOff () {
			return this.$router.push({ path: '/dashboard/calender/time-off/request' })
		},
		approveTimeOff () {
			return this.$router.push({ path: '/dashboard/calender/time-off/approve' })
		},
		gotoPast () {
			// let calendarApi = this.$refs.fullCalendar.getApi() // from the ref="..."
			// calendarApi.gotoDate('2000-01-01') // call a method on the Calendar object
		}
		// handleDateClick(arg) {
		//     if (confirm('Would you like to add an event to ' + arg.dateStr + ' ?')) {
		//         // this.calendarEvents.push({ // add new event data
		//         //     title: 'New Event',
		//         //     start: arg.date,
		//         //     allDay: arg.allDay
		//         // })
		//     }
		// }
	}
}

</script>

<style lang='scss'>
// you must include each plugins' css
// paths prefixed with ~ signify node_modules
@import '../../../../../node_modules/@fullcalendar/core/main.css';
@import '../../../../../node_modules/@fullcalendar/daygrid/main.css';
@import '../../../../../node_modules/@fullcalendar/timegrid/main.css';

.demo-app {
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;

}

.demo-app-top {
    margin: 0 0 3em;
}

.demo-app-calendar {
    margin: 0 auto;
    /*max-width: 900px;*/
    background-color: white;
}

</style>
