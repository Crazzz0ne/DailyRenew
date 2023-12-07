<template>
  <div class="row pt-4">
    <div class="col-12">
      <div class="text-center">
        <h5>King of the Hills </h5>
      </div>
    </div>
    <div class="col-md-6 order-md-4 order-md-2">
      <div class="pb-5">
        <MazSelect
          v-model="selectedType"
          :options="typeList"
          :size="'sm'"
          placeholder="Type"
          search-placeholder="Type"
          color="info"
          @input="getPositionCount()"
        >
          <i
            slot="icon-left"
            class="material-icons"
          >
            add_circle_outline
          </i>
        </MazSelect>
        <MazSelect
          v-if="labelLoaded"
          v-model="selectedPosition"
          class="py-3"
          :options="positionList"
          :size="'sm'"
          placeholder="Position"
          color="info"
          @input="getPositionCount()"
        >
          <i
            slot="icon-left"
            class="material-icons"
          >
            add_circle_outline
          </i>
        </MazSelect>
        <div class="text-center pt-2">
          <p>{{ dateRangeFormatted }}</p>
        </div>
        <p><strong>{{ selectedOfficeName }}</strong></p>
        <div v-if="Object.keys(details).length">
          <MazBtn
            v-if="$can('view lead')"
            class="maz-mr-2 maz-mb-2"
            @click="showModel = true"
          >
            Details
          </MazBtn>
          <MazDialog
            v-model="showModel"
          >
            <div slot="title">
              Details
            </div>
            <div>
              <MazSelect
                v-model="selectedUser"
                placeholder="Sales Rep"
                :options="repList"
              />
              <div
                v-if="selectedUser"
                class="lists-container"
              >
                <div class="list-1">
                  <h5 class="maz-mb-2">
                    <!--                                        How many days by {{ compType }}-->
                  </h5>
                  <p class="maz-text-muted maz-mb-3" />
                  <MazList>
                    <MazListItem
                      v-for="(lead, key) in displayLeads.leads"
                      :key="key"
                    >
                      <p>
                        Lead :{{ lead.id }} {{ lead.name }}
                        <MazBtn
                          :size="'mini'"
                          fab
                          @click="openLead(lead.id)"
                        >
                          <i class="fa fa-eye" />
                        </MazBtn>
                      </p>
                      <p class="maz-text-muted" />
                    </MazListItem>
                  </MazList>
                </div>
              </div>
            </div>
          </MazDialog>
        </div>
      </div>
    </div>
    <div class="col-md-6 order-md-4 order-md-4">
      <template v-if="!positionsLoading">
        <BarChart
          v-if="!positionsLoading"
          class="small"
          :chartdata="chartData"
          :options="positionOptions"
        />
      </template>
      <template v-else>
        <MazLoader />
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import BarChart from '../chart/BarChart'

export default {
	name: 'CloserBarChart',
	components: { BarChart },
	props: [
		'selectedOffice',
		'piePickerRangeValues',
		'dateRangeFormatted',
		'selectedUserName',
		'selectedOfficeName',
		'selectedRegion'

	],
	data () {
		return {
			showModel: false,
			selectedUser: null,
			positionCount: {
				datasets: [
					{
						data: [],
						backgroundColor: []
					}
				],

				labels: []

			},
			rawLabels: {},
			details: {},
			labelLoaded: true,
			selectedPosition: 1,
			positionList: [
				{ value: 1, label: 'Canvasser' },
				{ value: 2, label: 'Sp1' },
				{ value: 3, label: 'Sp2' }
				// {value: 4, label: 'Integrations'}
			],
			positionsLoading: false,
			selectedType: null,
			typeList: [
				{ value: null, label: 'Select One' },
				{ value: 'appointments', label: 'Appointments' },
				{ value: 'credit_pass', label: 'Credit Pass' },
				{ value: 'sit', label: 'Sat' },
				{ value: 'close', label: 'Close' },
				{ value: 'install', label: 'Install' }
			],
			tempList: [
				{ value: 'credit_pass_ratio', label: 'Credit Pass Ratio' },
				{ value: 'appointments_ratio', label: 'Appointments Ratio' },
				{ value: 'sit_ratio', label: 'Sat Ratio' }
			],
			positionOptions: {
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
			labels: 0
		}
	},
	computed: {

		ratio () {
			if (this.selectedType === 'appointments' || this.selectedType === 'credit_pass' ||
                this.selectedType === 'sit' || this.selectedType === 'close' || this.selectedType === 'install') {
				return false
			} else {
				return true
			}
		},

		chartData () {
			const datasets = []
			const graphXData = []
			const graphYData = []
			const colorlist = []

			if (this.rawData) {
				const color = this.colorList
				const i = 0
				const temp = 2

				return {
					datasets: [{
						borderColor: this.colorList,
						backgroundColor: this.colorList,
						data: this.rawData
					}],
					labels: this.labels

				}
			}
		},

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
		repList () {
			let u = {
				value: 'null', label: 'Select One'
			}
			const users = []
			users.push(u)
			if (Object.entries(this.details).length === 0) {
				return []
			}

			$.each(this.details, function (skey, value) {
				u = {
					value: skey, label: value.name
				}
				users.push(u)
			}
			)
			return users
		},
		displayLeads () {
			const user = [this.selectedUser]
			const filtered = Object.keys(this.details)
				.filter(key => user.includes(key))
				.reduce((obj, key) => {
					obj[key] = this.details[key]
					return obj
				}, {})

			const list = []
			return filtered[Object.keys(filtered)[0]]
			$.each(filtered, function (v, k) {
				console.log('k', k)
				// filtered.time.forEach((v, k) => {
				$.each(k.time, function (skey, value) {
					console.log('asdasd')
					list.push({ leadId: skey, days: value })
				})
			})
			// $.each(filtered.time, function (skey, value) {
			//   console.log(skey, value)
			// })

			return list
		}

	},
	watch: {
		selectedOffice: function (newVal, oldVal) {
			this.getPositionCount()
		},
		selectedRegion () {
			this.getPositionCount()
		},
		piePickerRangeValues: function (newVal, oldVal) {
			this.getPositionCount()
		}
	},
	created () {

	},
	methods: {

		openLead: function (id) {
			const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
			window.open(routeData.href, '_blank')
		},
		getPositionCount () {
			console.log('this Type', this.selectedType)
			if (this.selectedType != null) {
				this.details = {}
				this.selectedUser = null
				this.positionsLoading = true
				let temp = null
				temp = `/api/salesflow/reporting/count-position?office_id=${this.selectedOffice}&position=${this.selectedPosition}&type=${this.selectedType}`

				axios.get(temp,
					{
						params: {
							ratio: this.ratio,
							officeId: this.selectedOffice,
							regionId: this.selectedRegion,
							pickerRangeValues: this.piePickerRangeValues
						}
					})
					.then((response) => {
						// console.log(response, 'responce from kings')
						this.rawData = response.data.chartValues
						// this.positionCount.datasets[0].backgroundColor = response.data.backgroundColor;
						// this.positionCount.datasets[0].borderColor = response.data.backgroundColor;
						// this.positionCount.labels = response.data.labels;
						this.details = response.data.details
						this.labels = response.data.labels
						// // this.total = response.data.total
						this.positionsLoading = false
					})
					.catch(function (error) {
						console.log(error)
					})
			}
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
