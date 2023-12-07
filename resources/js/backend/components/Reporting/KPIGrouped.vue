<template>
  <div v-if="!loading">
    <div class="row">
      <div class="col">
        <user-office-select
          :can-view-regions="true"
          :can-view-offices="true"
          :user-market-id="regionId"
          :can-view-users="true"
          @officeChange="updateOffice($event)"
          @selectedRegionId="updateRegion($event)"
          @userChange="updateUser($event)"
        />
      </div>
      <div class="col">
        <mazSelect
          v-model="grouping"
          :options="groupingOptions"
          @blur="getRunChart()"
        />
        <mazSelect
          v-model="reference"
          :options="referenceOptions"
          @blur="getRunChart()"
        />
      </div>
    </div>
    <template
      v-if="reportData"
    >
      <LineChart
        v-if="datasets"
        :chartdata="datasets"
        :options="{responsive: true,
                   maintainAspectRatio: false}"
      />
      <div class="table-responsive">
        <table class="table mb-0 table-borderless table-striped">
          <thead>
            <tr class="h5">
              <th />
              <th
                v-for="(data, index, key) in dataComp"
                :key="key"
              >
                {{ data.display }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              class="py-3 px-6"
            >
              <td>
                Appointments {{ sumArray(appointmentsComp) }}
              </td>
              <td
                v-for="(v, k) in appointmentsComp"
                :key="k"
              >
                {{ v }}
              </td>
            </tr>
            <tr
              class="py-3 px-6"
            >
              <td>
                Credit Pass {{ sumArray(creditPassComp) }}
              </td>
              <td
                v-for="(v, k) in creditPassComp"
                :key="k"
              >
                {{ v }}
              </td>
            </tr>
            <tr
              class="py-3 px-6"
            >
              <td>
                Sat {{ sumArray(satComp) }}
              </td>
              <td
                v-for="(v, k) in satComp"
                :key="k"
              >
                {{ v }}
              </td>
            </tr>
            <tr
              class="py-3 px-6"
            >
              <td>
                Closed {{ sumArray(closedComp) }}
              </td>
              <td
                v-for="(v, k) in closedComp"
                :key="k"
              >
                {{ v }}
              </td>
            </tr>
            <tr
              class="py-3 px-6"
            >
              <td>
                Installs {{ sumArray(installsComp) }}
              </td>
              <td
                v-for="(v, k) in installsComp"
                :key="k"
              >
                {{ v }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="col">
          Terminated
          <MazSwitch v-model="terminatedFilter" />
        </div>
      </div>
      <MazCollapse class="maz-mb-5">
        <div slot="header-text">
          Appointments
        </div>
        <div class="maz-p-4 table-responsive">
          <table class="table mb-0 table-borderless table-striped">
            <thead>
              <tr class="h5">
                <th />
                <th
                  v-for="(data, index, key) in dataComp"
                  :key="key"
                >
                  {{ data.display }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  Appointments
                </td>
              </tr>
              <tr v-for="(person, index, key) in sortUsersByAppointment">
                <template
                  v-for="(results, i, k) in person.datesArray"
                >
                  <template
                    v-if="person.total.appointments > 0 && ((person.info.terminated && terminatedFilter) || !person.info.terminated )"
                  >
                    <td v-if="i === 0">
                      {{ person.info.name.full }}  <span v-if="person.info.terminated">💀</span>
                      <p>{{ person.total.appointments }}</p>
                    </td>
                    <td>
                      {{ results.appointments }}
                    </td>
                  </template>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </MazCollapse>
      <MazCollapse class="maz-mb-5">
        <div slot="header-text">
          Credit Pass
        </div>
        <div class="maz-p- table-responsive">
          <table class="table mb-0 table-borderless table-striped">
            <thead>
              <tr class="h5">
                <th />
                <th
                  v-for="(data, index, key) in dataComp"
                  :key="key"
                >
                  {{ data.display }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  Credit Pass
                </td>
              </tr>
              <tr v-for="(person, index, key) in sortUsersByCreditPass">
                <template
                  v-for="(results, i, k) in person.datesArray"
                >
                  <template v-if="person.total.creditPass > 0 && ((person.info.terminated && terminatedFilter) || !person.info.terminated )">
                    <td v-if="i === 0">
                      {{ person.info.name.full }} <span v-if="person.info.terminated">💀</span>
                      <p> {{ person.total.creditPass }}</p>
                    </td>
                    <td>
                      {{ results.creditPass }}
                    </td>
                  </template>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </MazCollapse>
      <MazCollapse class="maz-mb-5">
        <div slot="header-text">
          Sat
        </div>
        <div class="maz-p-4 table-responsive">
          <table class="table mb-0 table-borderless table-striped">
            <thead>
              <tr class="h5">
                <th />
                <th
                  v-for="(data, index, key) in dataComp"
                  :key="key"
                >
                  {{ data.display }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  Sat
                </td>
              </tr>
              <tr v-for="(person, index, key) in sortUsersBySit">
                <template v-if="person.total.sat > 0">
                  <template
                    v-for="(results, i, k) in person.datesArray"
                  >
                    <template v-if="person.total.sat > 0 && ((person.info.terminated && terminatedFilter) || !person.info.terminated )">
                      <td v-if="i === 0 && ((person.info.terminated && terminatedFilter) || !person.info.terminated )">
                        {{ person.info.name.full }} <span v-if="person.info.terminated">💀</span>
                        <p> {{ person.total.sat }}</p>
                      </td>
                      <td>
                        {{ results.sat }}
                      </td>
                    </template>
                  </template>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </MazCollapse>
      <MazCollapse class="maz-mb-5">
        <div slot="header-text">
          Closed
        </div>
        <div class="maz-p-4 table-responsive">
          <table class="table mb-0 table-borderless table-striped">
            <thead>
              <tr class="h5">
                <th />
                <th
                  v-for="(data, index, key) in dataComp"
                  :key="key"
                >
                  {{ data.display }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  Closed
                </td>
              </tr>
              <tr v-for="(person, index, key) in sortUsersByClosed">
                <template v-if="!person.info.terminated">
                  <template
                    v-for="(results, i, k) in person.datesArray"
                  >
                    <template v-if="person.total.closed > 0 && ((person.info.terminated && terminatedFilter) || !person.info.terminated )">
                      <td v-if="i === 0">
                        {{ person.info.name.full }} <span v-if="person.info.terminated">💀</span>
                        <p> {{ person.total.closed }}</p>
                      </td>
                      <td>
                        {{ results.closed }}
                      </td>
                    </template>
                  </template>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </MazCollapse>
      <MazCollapse class="maz-mb-5">
        <div slot="header-text">
          Installed
        </div>
        <div class="maz-p-4 table-responsive">
          <table class="table mb-0 table-borderless table-striped">
            <thead>
              <tr class="h5">
                <th />
                <th
                  v-for="(data, index, key) in dataComp"
                  :key="key"
                >
                  {{ data.display }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  Installed
                </td>
              </tr>
              <tr v-for="(person, index, key) in sortUsersByInstalls">
                <template
                  v-for="(results, i, k) in person.datesArray"
                >
                  <template v-if="person.total.installs > 0 && ((person.info.terminated && terminatedFilter) || !person.info.terminated )">
                    <td v-if="i === 0 ">
                      <p>{{ person.info.name.full }} <span v-if="person.info.terminated">💀</span></p>
                      {{ person.total.installs }}
                    </td>
                    <td>
                      <p>{{ results.installs }}</p>
                    </td>
                  </template>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </MazCollapse>
    </template>
  </div>
</template>

<script>
import KPITable from '../Chat/KPI/KPITable'
import axios from 'axios'
import LineChart from '../chart/LineChart'
import { mapActions, mapGetters } from 'vuex'
import UserOfficeSelect from '../Selects/UserOfficeSelect'

export default {
	name: 'KPIGrouped',
	components: { UserOfficeSelect, LineChart },
	data () {
		return {
			reportData: null,
			totals: null,
			scope: 'office',
			regionId: 0,
			officeId: 0,
			userId: 0,
			loading: true,
			grouping: 'W',
			groupingOptions: [{ label: 'Daily', value: 'd' },
				{ label: 'Weekly', value: 'W' },
				{ label: 'Monthly', value: 'M' }],
			reference: 'createdAt',
			referenceOptions: [
				{ label: 'By Appointment Created At', value: 'createdAt' },
				{ label: 'By Appointment Date', value: 'startTime' }
			],
			terminatedFilter: false,
			draw: 0

		}
	},

	computed: {
		...mapGetters([
			'getUser'
		]),
		dataComp () {
			const data = []
			if (!this.totals) {
				return null
			}
			Object.entries(this.totals).forEach(([key, value]) => {
				data.push(value)
			})
			return data.sort((a, b) => {
				return a.sort - b.sort
			})
		},
		reportComp () {
			const data = []
			if (!this.reportData) {
				return null
			}
			Object.entries(this.reportData).forEach(([key, value]) => {
				const grouped = []
				Object.entries(value.datesArray).forEach(([k, v]) => {
					grouped.push(v)
				})
				const groupSorted = grouped.sort((a, b) => {
					return a.sort - b.sort
				})
				data.push({ datesArray: groupSorted, total: value.total, info: value.info })
			})
			return true
		},
		sortUsersByAppointment () {
			const data = []
			if (!this.reportData) {
				return null
			}
			Object.entries(this.reportData).forEach(([key, value]) => {
				const grouped = []
				Object.entries(value.datesArray).forEach(([k, v]) => {
					grouped.push(v)
				})
				const groupSorted = grouped.sort((a, b) => {
					return a.sort - b.sort
				})
				data.push({ datesArray: groupSorted, total: value.total, info: value.info })
			})
			return data.sort((a, b) => {
				return b.total.appointments - a.total.appointments
			})
		},
		sortUsersByCreditPass () {
			const data = []
			if (!this.reportData) {
				return null
			}
			Object.entries(this.reportData).forEach(([key, value]) => {
				const grouped = []
				Object.entries(value.datesArray).forEach(([k, v]) => {
					grouped.push(v)
				})
				const groupSorted = grouped.sort((a, b) => {
					return a.sort - b.sort
				})
				data.push({ datesArray: groupSorted, total: value.total, info: value.info })
			})
			return data.sort((a, b) => {
				return b.total.creditPass - a.total.creditPass
			})
		},
		sortUsersBySit () {
			const data = []
			if (!this.reportData) {
				return null
			}
			Object.entries(this.reportData).forEach(([key, value]) => {
				const grouped = []
				Object.entries(value.datesArray).forEach(([k, v]) => {
					grouped.push(v)
				})
				const groupSorted = grouped.sort((a, b) => {
					return a.sort - b.sort
				})
				data.push({ datesArray: groupSorted, total: value.total, info: value.info })
			})
			return data.sort((a, b) => {
				return b.total.sat - a.total.sat
			})
		},
		sortUsersByClosed () {
			const data = []
			if (!this.reportData) {
				return null
			}
			Object.entries(this.reportData).forEach(([key, value]) => {
				const grouped = []
				Object.entries(value.datesArray).forEach(([k, v]) => {
					grouped.push(v)
				})
				const groupSorted = grouped.sort((a, b) => {
					return a.sort - b.sort
				})
				data.push({ datesArray: groupSorted, total: value.total, info: value.info })
			})
			return data.sort((a, b) => {
				return b.total.closed - a.total.closed
			})
		},
		sortUsersByInstalls () {
			const data = []
			if (!this.reportData) {
				return null
			}
			Object.entries(this.reportData).forEach(([key, value]) => {
				const grouped = []
				Object.entries(value.datesArray).forEach(([k, v]) => {
					grouped.push(v)
				})
				const groupSorted = grouped.sort((a, b) => {
					return a.sort - b.sort
				})
				data.push({ datesArray: groupSorted, total: value.total, info: value.info })
			})
			return data.sort((a, b) => {
				return b.total.installs - a.total.installs
			})
		},

		labels () {
			if (!this.dataComp) {
				return null
			}
			const array = []
			this.dataComp.forEach((v) => {
				array.push(v.display)
			})
			return array
		},
		installsComp () {
			const installs = []
			if (!this.totals) {
				return null
			}
			Object.entries(this.totals).forEach(([key, value]) => {
				installs.push(value.installs)
			})
			return installs
		},

		appointmentsComp () {
			const appointments = []
			if (!this.totals) {
				return null
			}
			Object.entries(this.totals).forEach(([key, value]) => {
				appointments.push(value.appointments)
			})
			return appointments
		},
		satComp () {
			const installs = []
			if (!this.totals) {
				return null
			}
			Object.entries(this.totals).forEach(([key, value]) => {
				installs.push(value.sat)
			})
			return installs
		},
		closedComp () {
			const installs = []
			if (!this.totals) {
				return null
			}
			Object.entries(this.totals).forEach(([key, value]) => {
				installs.push(value.closed.value)
			})
			return installs
		},
		creditPassComp () {
			const installs = []
			if (!this.totals) {
				return null
			}
			Object.entries(this.totals).forEach(([key, value]) => {
				installs.push(value.creditPass)
			})
			return installs
		},

		datasets () {
			return {
				labels: this.labels,
				datasets: [{
					label: 'Appointments',
					data: this.appointmentsComp,
					fill: false,
					borderColor: 'rgb(75, 192, 192)',
					tension: 0.1
				},
				{
					label: 'Credit Passes',
					data: this.creditPassComp,
					fill: false,
					borderColor: 'rgb(164,61,198)',
					tension: 0.1
				},
				{
					label: 'Sits',
					data: this.satComp,
					fill: false,
					borderColor: 'rgb(83,198,61)',
					tension: 0.1
				},
				{
					label: 'Closes',
					data: this.closedComp,
					fill: false,
					borderColor: 'rgb(0,62,255)',
					tension: 0.1
				},
				{
					label: 'Installs',
					data: this.installsComp,
					fill: false,
					borderColor: 'rgb(255,242,0)',
					tension: 0.1
				}

				]
			}
			if (!this.totals) {
				return null
			}
			const appointments = []
			const sat = []
			const closed = []
			const creditPass = []
			const installs = []
			Object.entries(this.totals).forEach(([key, value]) => {
				appointments.push(value.appointments)
				sat.push(value.sat)
				closed.push(value.closed)
				creditPass.push(value.creditPass)
				installs.push(value.installs)
			})
			const datasets = [
				{
					label: 'Appointments',
					data: appointments
				}
			]

			return datasets
		}
	},
	watch: {
		reference ($old, $new) {
			this.getRunChart()
		},
		grouping ($old, $new) {
			this.getRunChart()
		}
	},
	mounted () {
		this.fetchUsers()
			.then(() => {
				if (this.$can('administrate company')) {
					this.scope = ''
				} else if (this.$can('manage region')) {
					this.scope = 'region'
					this.regionId = this.getUser.data.office.region_id
				} else if (this.$can('view office')) {
					this.scope = 'office'
					this.officeId = this.getUser.data.office.id
				} else {
					this.scope = 'user'
					this.officeId = this.getUser.data.id
				}
				this.getRunChart()
				this.loading = false
			})
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		userRow (id) {

		},
		sumOfAppointments (array) {

		},
		getRunChart () {
			this.draw++
			const data = {
				officeId: this.officeId,
				regionId: this.regionId,
				userId: this.userId,
				scope: this.scope,
				by: this.reference,
				grouping: this.grouping,
				draw: this.draw
			}
			this.reportData = null
			axios.post('/api/reporting/run-chart', data)
				.then((response) => {
					console.log(this.draw, response.data.draw)
					if (response.data.draw === this.draw) {
						this.reportData = response.data.report
						this.totals = response.data.totals
					}
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		sumArray (array) {
			return array.reduce((a, b) => a + b)
		},
		updateOffice (event) {
			if (event) {
				this.officeId = event
				this.scope = 'office'
				this.getRunChart()
			} else {
				this.scope = 'region'
				this.updateRegion(this.regionId)
			}
		},
		updateRegion (event) {
			this.regionId = event
			this.scope = 'region'
			this.getRunChart()
		},
		updateUser (event) {
			this.userId = event
			if (event) {
				this.scope = 'user'
				this.getRunChart()
			} else {
				this.scope = 'office'
				this.updateOffice(this.officeId)
			}
		}
	}

}
</script>

<style scoped>

</style>
