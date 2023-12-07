<template>
  <div>
    <div v-if="reportData.length">
      <div>
        <div class="px-4 pb-4 mb-6 rounded shadow">
          <MazPicker
            v-model="selectedDates"
            placeholder="Select date range"
            :no-time="true"
            :formatted="'MMMM Do YYYY'"
            range
            :locale="'locale'"
            @validate="getKPI()"
          >
            <i
              slot="icon-left"
              class="material-icons"
            >
              date_range
            </i>
          </MazPicker>
          <div class="row">
            <div class="col">
              <MazSelect
                v-model="selectedPosition"
                :options="positionList"
                @blur="getKPI()"
              />
            </div>
            <div class="col">
              <MazSelect
                v-model="selectedOffice"
                :options="officeList"
              />
            </div>
            <div class="col">
              <MazSelect />
            </div>
          </div>
          <div
            v-for="(office, index) in groupByOffice"
            :key="index"
          >
            <k-p-i-table
              :first-row="groupedTotal(office)"
              :chart-data="office"
              :offices="offices"
              :teams="teams"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import KPITable from '../../components/Chat/KPI/KPITable'
import KPIGrouped from '../../components/Reporting/KPIGrouped'

export default {
	name: 'KPI',
	components: { KPIGrouped, KPITable },
	data () {
		return {
			selectedDates: {
				start: null,
				end: null
			},
			reportData: [],
			teams: {},
			offices: [],
			selectedPosition: 3,
			selectedOffice: null,
			positionList: [
				{ label: 'Select a Position', value: null },
				{ label: 'Opener', value: 1 },
				{ label: 'Sp1', value: 2 },
				{ label: 'Sp2', value: 3 }
			],
			sortOrder: null,
			withTerminated: true,
			groupBy: 52,
			groupByType: 'office'
		}
	},
	computed: {

		groupByOffice () {
			return	this.ratios.reduce((r, a) => {
				r[a.office_id] = r[a.office_id] || []
				r[a.office_id].push(a)
				return r
			}, Object.create(null))
		},

		ratios () {
			let ratios = this.reportData.map((b) => {
				if (b.sat) {
					b.appointmentToSit = b.sat / b.appointments * 100
				} else {
					b.appointmentToSit = 0
				}

				if (b.creditPass && b.closed) {
					b.creditPassToClose = b.closed / b.creditPass * 100
					b.sitToClose = b.closed / b.sat * 100
				} else {
					b.sitToClose = 0
					b.creditPassToClose = 0
				}
				if (b.closed) {
					b.appointmentToClose = b.closed / b.appointments * 100
				} else {
					b.appointmentToClose = 0
				}
				if (b.installs) {
					b.closeToInstall = b.installs / b.closed * 100
				} else {
					b.closeToInstall = 0
				}

				return b
			})

			if (this.selectedOffice) {
				console.log(this.selectedOffice)
				ratios = ratios.filter(x => x.office_id === this.selectedOffice)
			}
			if (!this.withTerminated) {
				ratios = ratios.filter(x => x.terminated === false)
			}

			switch (this.sortOrder) {
			case 'name':
				return ratios.sort((a, b) => b.name.first - a.name.first)
			case 'appointment to sit':
				return ratios.sort((a, b) => b.appointmentToSit - a.appointmentToSit)
			case 'appointment to close':
				return ratios.sort((a, b) => b.appointmentToClose - a.appointmentToClose)
			case 'credit pass to close':
				return ratios.sort((a, b) => b.creditPassToClose - a.creditPassToClose)
			case 'sit to close':
				return ratios.sort((a, b) => b.sitToClose - a.sitToClose)
			case 'close to install':
				return ratios.sort((a, b) => b.closeToInstall - a.closeToInstall)
			}

			return ratios.sort((a, b) => b.appointmentToClose - a.appointmentToClose)
		},
		officeList () {
			const list = this.offices.map(b => {
				b.value = b.id
				b.label = b.name
				return b
			})
			const none = { value: null, label: 'Select an Office' }
			list.unshift(none)
			return list
		}
		// context () {
		// 	const start = 0
		// 	const closedTotal = this.ratios.reduce((total, currentValue) => total + currentValue.closed, start)
		// 	const satTotal = this.ratios.reduce((total, currentValue) => total + currentValue.sat, start)
		// 	const creditPassTotal = this.ratios.reduce((total, currentValue) => total + currentValue.creditPass, start)
		//     const creditPassTotal = this.ratios.reduce((total, currentValue) => total + currentValue.creditPass, start)
		// 	return closedTotal
		// }
	},
	created () {
		this.setTimeRange()
	},
	mounted () {
		this.getKPI()
	},
	methods: {
		displayPercent (data) {
			return data.toFixed(0)
		},
		groupedTotal (group) {
			const returnData = {
				appointments: 0,
				appointmentToSit: 0,
				closed: 0,
				creditPass: 0,
				creditPassToClose: 0,
				installs: 0,
				sitToClose: 0,
				sat: 0,
				installed: 0,
				name: { full: this.officeName(group[0].office_id) }

			}
			let i = 0
			group.forEach((b) => {
				returnData.appointments += b.appointments
				returnData.appointmentToSit += b.appointmentToSit
				returnData.creditPass += b.creditPass
				returnData.creditPassToClose += b.creditPassToClose
				returnData.installs += b.installs
				returnData.sitToClose += b.sitToClose
				returnData.sat += b.sat
				returnData.closed += b.closed
				returnData.appointmentToClose += b.appointmentToClose
				i++
			})
			if (returnData.appointments) {
				returnData.appointmentToSit = returnData.sat / returnData.appointments * 100
			} else {
				returnData.appointmentToSit = 0
			}
			if (returnData.creditPass) {
				returnData.creditPassToClose = returnData.closed / returnData.creditPass * 100
			} else {
				returnData.creditPassToClose = 0
			}
			if (returnData.closed) {
				returnData.sitToClose = returnData.closed / returnData.sat * 100
				returnData.appointmentToClose = returnData.closed / returnData.appointments * 100
			} else {
				returnData.appointmentToClose = 0
				returnData.sitToClose = 0
			}
			if (returnData.installs) {
				returnData.closeToInstall = returnData.installs / returnData.closed * 100
			} else {
				returnData.closeToInstall = 0
			}
			return returnData
		},
		teamName (id) {
			const team = this.teams.find(b => {
				return b.id === id
			})
			if (team) {
				return team.name
			} else {
				return ''
			}
		},
		officeName (id) {
			const office = this.offices.find(b => {
				return b.id === id
			})
			if (office) {
				return office.name
			} else {
				return ''
			}
		},
		setTimeRange () {
			this.selectedDates.start = this.$dayjs().startOf('month').format('YYYY-MM-DD')
			this.selectedDates.end = this.$dayjs().format('YYYY-MM-DD')
		},
		getKPI () {
			const date = {
				dateRange: this.selectedDates,
				positionId: this.selectedPosition
			}
			axios.post('/api/reporting/kpi', date)
				.then((response) => {
					this.reportData = response.data.report
					this.teams = response.data.teams
					this.offices = response.data.offices
				})
				.catch(function (error) {
					console.log(error)
				})
			this.userLoaded = true
		}
	}

}
</script>

<style scoped>

</style>
