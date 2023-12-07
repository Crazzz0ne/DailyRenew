<template>
  <div class="table-responsive">
    <table class="table mb-0 table-borderless table-striped">
      <thead>
        <tr class="h5">
          <th
            class="pt-4 pb-3 px-6 pe-auto"
            @click="sortOrder('name')"
          >
            Solar Expert
          </th>
          <th
            class="pt-4 pb-3 px-6 pe-auto"
            @click="sortOrder('appointment to sit')"
          >
            Appointment To Sit
          </th>
          <th
            class="pt-4 pb-3 px-6 pe-auto"
            @click="sortOrder('appointment to close')"
          >
            Appointments To Close
          </th>
          <th
            class="pt-4 pb-3 px-6 pe-auto"
            @click="sortOrder('credit pass to close')"
          >
            Credit Pass To Close
          </th>
          <th
            class="pt-4 pb-3 px-6 pe-auto"
            @click="sortOrder('sit to close')"
          >
            Sit To Close
          </th>
          <th
            class="pt-4 pb-3 px-6 pe-auto"
            @click="sortOrder('close to install')"
          >
            Close To Install
          </th>
          <th class="pt-4 pb-3 px-6">
            Appointments
          </th>
          <th class="pt-4 pb-3 px-6">
            Credit Passes
          </th>
          <th class="pt-4 pb-3 px-6">
            Sits
          </th>
          <th class="pt-4 pb-3 px-6">
            Closes
          </th>
          <th class="pt-4 pb-3 px-6">
            Installs
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(data, index) in displayData"
          :key="index"
        >
          <td class="py-3 px-6">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0">
                  {{ data.name.full }}
                </p>
                <p
                  v-if="teamName(data.team_id)"
                  class="mb-0 "
                >
                  Team: {{ teamName(data.team_id) }}
                </p>
              </div>
            </div>
          </td>
          <td class="py-5 px-6">
            {{ displayPercent(data.appointmentToSit) }}%
          </td>
          <td class="py-5 px-6">
            {{ displayPercent(data.appointmentToClose) }}%
          </td>
          <td class="py-5 px-6">
            {{ displayPercent(data.creditPassToClose) }}%
          </td>
          <td class="py-5 px-6">
            {{ displayPercent(data.sitToClose) }}%
          </td>
          <td class="py-5 px-6">
            {{ displayPercent(data.closeToInstall) }}%
          </td>
          <td class="py-5 px-6">
            {{ data.appointments }}
          </td>
          <td class="py-5 px-6">
            {{ data.creditPass }}
          </td>
          <td class="py-5 px-6">
            {{ data.sat }}
          </td>
          <td class="py-5 px-6">
            {{ data.closed }}
          </td>
          <td class="py-5 px-6">
            {{ data.installs }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
	name: 'KPITable',
	props: [
		'chartData',
		'teams',
		'offices',
		'firstRow'
	],
	computed: {
		displayData () {
			const tempArray = this.chartData
			if (this.firstRow) {
				tempArray.unshift(this.firstRow)
			}
			return tempArray
		}
	},
	methods: {
		displayPercent (data) {
			return data.toFixed(0)
		},
		sortOrder (name) {
			this.$emit('sortOrder', name)
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
		}
	}
}
</script>

<style scoped>

</style>
