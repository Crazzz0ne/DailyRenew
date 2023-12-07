<template>
  <div v-if="compCityList">
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">
            City
          </th>
          <th scope="col">
            Close Percent
          </th>
          <th scope="col">
            Sit Percent
          </th>
          <th scope="col">
            Average KwH
          </th>
          <th scope="col">
            Closers
          </th>
          <th scope="col">
            Collective days on
          </th>
          <th scope="col">
            Past Appointments
          </th>
          <th scope="col">
            Future Appointments
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(data) in compCityList">
          <th scope="row">
            {{ data.name }}
          </th>
          <td>{{ data.closePrecent }}%</td>
          <td>{{ data.sitPrecent }}%</td>
          <td>{{ data.avgKWH }}</td>
          <td>{{ data.userCount }} </td>
          <td>{{ data.availability }} </td>
          <td>{{ data.appointments }} </td>
          <td>{{ data.futureAppointments }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'CityList',
	data () {
		return {
			rawCityList: []
		}
	},
	computed: {
		compCityList () {
			const list = this.rawCityList.map((b) => {
				const closePercent = b.closed / b.appointments * 100
				const avgKWH = b.closed / b.kwh * 100
				const sitAverage = b.sat / b.appointments * 100
				if (isNaN(closePercent)) {
					b.closePrecent = 0
					b.closePercentRaw = 0
				} else {
					b.closePrecent = closePercent.toFixed(0) ?? 0
					b.closePercentRaw = closePercent
				}
				if (isNaN(closePercent)) {
					b.sitPrecent = 0
					b.sitPercentRaw = 0
				} else {
					b.sitPrecent = sitAverage.toFixed(0) ?? 0
					b.sitPercentRaw = sitAverage
				}
				if (isNaN(avgKWH)) {
					b.avgKWH = 0
					b.avgKWHRaw = 0
				} else {
					b.avgKWH = avgKWH.toFixed(0) ?? 0
					b.avgKWHRaw = avgKWH
				}

				return b
			})
			return list.sort((a, b) => {
				return b.closePercentRaw - a.closePercentRaw
			})
		}

	},
	created () {
		this.getCoverage()
	},
	methods: {
		getCoverage () {
			axios.get('/api/round-robin/cities')
				.then((response) => {
					this.rawCityList = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
