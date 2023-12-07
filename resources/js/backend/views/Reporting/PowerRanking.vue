<template>
  <div>
    <div class="">
      <div class="card">
        <div class="card-header text-center">
          <h1>Power Ranking</h1>
        </div>
        <div class="card-body">
          <div class="row justify-content-center pb-5">
            <div class="col-md-3">
              <MazSelect
                v-model="positionId"
                :options="positions"
                :placeholder="'Select a position'"
              />
              <MazBtnGroup
                v-model="btnGroupValue"
                class="mt-3"
                :items="btnTypeList"
                outline
                size="md"
                color="second"
                @input="getRankings()"
              />
            </div>
            <div class="col-md-3">
              <MazSelect
                v-model="currentMonth"
                :options="months"
                :placeholder="'Select a month'"
              />
            </div>
            <div class="col-md-3">
              <MazSelect
                v-model="currentYear"
                :options="years"
                :placeholder="'Select a year'"
              />
            </div>
          </div>
          <div
            v-if="!chartLoading"
            class="row justify-content-between "
          >
            <div class="col-md-12 col-lg-6 col-xl-5">
              <BarChart
                :chartdata="chartData"
                :options="chartOptions"
              />
            </div>
            <div class="col-md-12 col-lg-6">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">
                        Power Ranking
                      </th>
                      <th scope="col">
                        Name
                      </th>
                      <th scope="col">
                        Points
                      </th>
                      <th scope="col">
                        Sits
                      </th>
                      <th scope="col">
                        Appointments
                      </th>
                      <th scope="col">
                        Credit Pass
                      </th>
                      <th scope="col">
                        U-Bill
                      </th>
                      <th scope="col">
                        Closed
                      </th>
                      <th scope="col">
                        No Show
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(value, index) in sortedData">
                      <th scope="row">
                        {{ index + 1 }}
                      </th>
                      <td>{{ value.name }}</td>
                      <td>{{ value.points }}</td>
                      <td>{{ value.sat }}</td>
                      <td>{{ value.appointments }}</td>
                      <td>{{ value.creditPass }}</td>
                      <td>{{ value.ubill }}</td>
                      <td>{{ value.close }}</td>
                      <td>{{ value.noShow }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div v-else>
            <MazLoader />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BarChart from '../../components/chart/BarChart'
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'PowerRanking',
	components: { BarChart },
	data () {
		return {
			currentMonth: 1,
			currentYear: 2023,
			positionId: 1,
			positions: [
				{
					label: 'Select One',
					value: null

				},
				{
					label: 'Opener',
					value: 1

				},
				{
					label: 'Sp1',
					value: 2

				}
			],
			years: [
				{
					label: 2020,
					value: 2020
				},
				{
					label: 2021,
					value: 2021
				},
				{
					label: 2022,
					value: 2022
				},
                {
                    label: 2023,
                    value: 2023
                }
			],
			months: [
				{
					label: 'Jan',
					value: 1
				},
				{
					label: 'Feb',
					value: 2
				},
				{
					label: 'Mar',
					value: 3
				},
				{
					label: 'Apr',
					value: 4
				},
				{
					label: 'May',
					value: 5
				},
				{
					label: 'Jun',
					value: 6
				},
				{
					label: 'Jul',
					value: 7
				},
				{
					label: 'Aug',
					value: 8
				},
				{
					label: 'Sep',
					value: 9
				},
				{
					label: 'Oct',
					value: 10
				},
				{
					label: 'Nov',
					value: 11
				},
				{
					label: 'Dec',
					value: 12
				}
			],
			chartLoading: true,
			chartOptions: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true
						}
					}]

				},
				legend: {
					display: false
				}
			},
			rawData: [],
			officeId: 34,
			btnTypeList: [
				{ label: 'Office', value: 'office' },
				{ label: 'All', value: 'all' }
			],
			btnGroupValue: 'office'
			// colorList: ['#35e62c', '#60ea21', '#7fee14', '#7fee14',
			//     '#b2f600', '#c9f900', '#c9f900',
			//     '#fde500', '#ffca00', '#ffca00', '#ff9100',
			//     '#ff7100', '#ff7100']
		}
	},
	watch: {
		currentMonth () {
			this.getRankings()
		},
		curentyear () {
			this.getRankings()
		},
		positionId () {
			this.getRankings()
		}
	},
	created () {
		const date = new Date()
		this.currentMonth = date.getMonth() + 1
		this.curentyear = date.getFullYear()
		this.fetchUsers().then(a => {
			this.getRankings()
		})
	},

	computed: {
		...mapGetters([
			'getUser'
		]),
		colorList () {
			let colors
			const rawData = Object.values(this.rawData)
			const count = rawData.length
			console.log(count, 'count')
			if (count > 20) {
				const colorCount = count / 3
				colors = ColorSteps.getColorSteps('green', 'orange', colorCount)
				const color2 = ColorSteps.getColorSteps('orange', 'yellow', colorCount)
				const color3 = ColorSteps.getColorSteps('yellow', 'red', colorCount)
				return colors.concat(color2, color3)
			} else if (count > 10) {
				const colorCount = count / 2
				colors = ColorSteps.getColorSteps('green', 'yellow', colorCount)

				const colors2 = ColorSteps.getColorSteps('yellow', 'red', colorCount)
				return colors.concat(colors2)
			} else {
				return colors = ColorSteps.getColorSteps('green', 'red', count)
			}

			console.log('color', colors)

			console.log('color', colors)
			colors = ColorSteps.getColorSteps('hsl(180, 50%, 50%)', 'rgba(200,100,20,0.5)', 10)
			console.log('color', colors)
		},
		chartData () {
			const datasets = []
			const graphXData = []
			const graphYData = []
			const colorlist = []

			if (this.rawData) {
				const color = this.colorList
				let i = 0
				let temp = 2
				console.log('temp', temp)
				temp = Math.round(temp)
				console.log(temp, 'temp')
				this.sortedData.forEach(function (value) {
					graphYData.push(value.points)
					graphXData.push(value.name)
					colorlist.push(color[i])

					i++

					// console.log(isOdd(i), 'odd')

					// console.log(i, 'i');
				})

				const payload = {
					datasets: [{
						borderColor: colorlist,
						backgroundColor: colorlist,
						data: graphYData
					}],
					labels: graphXData
					// colorRangeInfo: {
					//     colorStart: 0,
					//     colorEnd: 1,
					//     useEndAsStart: false,
					// },
					// scale: d3.interpolateBlues,

				}

				return payload
			}
		},
		sortedData () {
			const rawData = Object.values(this.rawData)

			return rawData.sort(function (a, b) {
				return b.points - a.points
			})
		}
	},

	methods: {
		...mapActions([
			'fetchUsers'
		]),
		getRankings () {
			this.chartLoading = true
			// if (this.selectedType != null) {
			this.details = {}
			this.selectedUser = null

			let temp = null
			temp = '/api/salesflow/reporting/power-ranking'
			let regionIds = null
			let officeId = null
			if (this.btnGroupValue === 'all') {
				regionIds = this.getUser.data.office.region_id
			} else {
				officeId = this.getUser.data.office.id
			}

			axios.get(temp,
				{
					params: {
						office_id: officeId,
						regionId: regionIds,
						year: this.currentYear,
						month: this.currentMonth,
						positionId: this.positionId
					}
				})
				.then((response) => {
					this.rawData = response.data
					this.chartLoading = false
				})
				.catch(function (error) {
					console.log(error)
				})
			// }
		}
	}
}
const ColorSteps = (() => {
	/**
     * Convert any color string to an [r,g,b,a] array.
     * @author Arjan Haverkamp (arjan-at-avoid-dot-org)
     * @param {string} color Any color. F.e.: 'red', '#f0f', '#ff00ff', 'rgb(x,y,x)', 'rgba(r,g,b,a)', 'hsl(180, 50%, 50%)'
     * @returns {array} [r,g,b,a] array. Caution: returns [0,0,0,0] for invalid color.
     * @see https://gist.github.com/av01d/8f068dd43447b475dec4aad0a6107288
     */
	const colorValues = color => {
		const div = document.createElement('div')
		div.style.backgroundColor = color
		document.body.appendChild(div)
		let rgba = getComputedStyle(div).getPropertyValue('background-color')
		div.remove()

		if (rgba.indexOf('rgba') === -1) {
			rgba += ',1' // convert 'rgb(R,G,B)' to 'rgb(R,G,B)A' which looks awful but will pass the regxep below
		}

		return rgba.match(/[\.\d]+/g).map(a => {
			return +a
		})
	}

	/**
     * Get color steps (gradient) between two colors.
     * @author Arjan Haverkamp (arjan-at-avoid-dot-org)
     * @param {string} colorStart Any color. F.e.: 'red', '#f0f', '#ff00ff', 'rgb(x,y,x)', 'rgba(r,g,b,a)', 'hsl(180, 50%, 50%)'
     * @param {string} colorEnd Any color
     * @param {int} steps Number of color steps to return
     * @returns {array} Array of 'rgb(r,g,b)' or 'rgba(r,g,b,a)' arrays
     */
	const getColorSteps = (colorStart, colorEnd, steps) => {
		const start = colorValues(colorStart)
		const end = colorValues(colorEnd)
		const opacityStep = (end[3] * 100 - start[3] * 100) / steps
		const colors = []
		let alpha = 0; let opacity = start[3] * 100

		for (let i = 0; i < steps; i++) {
			alpha += 1.0 / steps
			opacity += opacityStep

			const c = [
				Math.round(end[0] * alpha + (1 - alpha) * start[0]),
				Math.round(end[1] * alpha + (1 - alpha) * start[1]),
				Math.round(end[2] * alpha + (1 - alpha) * start[2])
			]

			colors.push(
				opacity == 100 ? `rgb(${c[0]},${c[1]},${c[2]})` : `rgba(${c[0]},${c[1]},${c[2]},${opacity / 100})`
			)
		}

		return colors
	}

	return {
		colorValues,
		getColorSteps
	}
})()
</script>

<style scoped>

</style>
