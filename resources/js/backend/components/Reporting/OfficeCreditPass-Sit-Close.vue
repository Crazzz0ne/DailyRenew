<template>
  <div class="row pt-4">
    <div class="col-12">
      <div class="text-center">
        <h5>Sit to Close Ratio</h5>
      </div>
    </div>
    <div class="col-6">
      <div class="pb-5">
        <div class="text-center pt-2" />
        <p>{{ dateRangeFormatted }}</p>
        <p><strong>{{ selectedOfficeName }}</strong></p>
        <div class="row">
          <div class="col-md-4">
            <label>Credit Pass to Sit</label>

            <MazCheckbox
              v-model="creditPass"
              class="mx-auto"
              @input="setCreditPass($event)"
            />
          </div>
          <div class="col-md-4">
            <label>Closes to Sit</label>

            <MazCheckbox
              v-model="closes"
              class="mx-auto"
              @input="setClose($event)"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <template v-if="!positionsLoading">
        <BarChart
          v-if="!positionsLoading"
          class="small"
          :chartdata="positionCount"
          :options="positionOptions"
        />
      </template>
      <template v-else-if="positionCount.datasets.length">
        Empty
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
import TimeToEventModel from './TimeToEventModel'

export default {
	name: 'OfficeCreditPassSitClose',
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
			positionCount: {
				datasets: [
					{
						data: [1, 1, 1, 2],
						backgroundColor: [
							'green',
							'red',
							'blue',
							'brown'
						]
					}
				],

				labels: ['Tom', 'Jerry', 'Credit Pass', 'Negotiating System']

			},
			details: [],
			showDetails: false,
			labelLoaded: true,
			selectedPosition: 1,
			positionList: [
				{ value: 1, label: 'Canvasser' },
				{ value: 2, label: 'Sp1' },
				{ value: 3, label: 'Sp2' }
				// {value: 4, label: 'Integrations'}
			],
			positionsLoading: true,
			// selectedType: 'close',
			// typeList: [
			//     {value: null, label: 'Select '},
			//     {value: 'all', label: 'Start to Install'},
			//     {value: 'close', label: 'Start to Close'},
			//     {value: 'install', label: 'Close to Install'},
			//     {value: 'site-survey', label: 'Site Survey to Install'}
			// ],

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
			creditPass: false,
			closes: true

		}
	},
	computed: {
		barType () {
			switch (this.selectedPosition) {
			case 1:
				return ''
			}
		}
	},
	watch: {
		selectedOffice: function (newVal, oldVal) {
			this.getPositionCount()
		},
		selectedRegion: function (newVal, oldVal) {
			this.getPositionCount()
		},

		piePickerRangeValues: function (newVal, oldVal) {
			this.getPositionCount()
		}
	},
	created () {
		this.getPositionCount()
	},
	methods: {
		setClose (val) {
			console.log(val)
			if (val) {
				this.creditPass = false
			} else {
				this.creditPass = true
			}

			this.getPositionCount()
		},
		setCreditPass (val) {
			console.log(val)
			if (val) {
				this.closes = false
			} else {
				this.closes = true
			}
			this.getPositionCount()
		},
		getPositionCount () {
			this.positionsLoading = true
			let temp = null
			temp = '/api/salesflow/reporting/sit-ratio-office'

			axios.get(temp,
				{
					params: {
						type: this.selectedType,
						region_id: this.selectedRegion,
						office_id: this.selectedOffice,
						position: this.selectedPosition,
						pickerRangeValues: this.piePickerRangeValues,
						closed: this.closes
					}
				})
				.then((response) => {
					if (response.data) {
						this.positionCount.datasets[0].data = response.data.chartValues
						this.positionCount.datasets[0].backgroundColor = response.data.backgroundColor
						this.positionCount.datasets[0].borderColor = response.data.backgroundColor
						this.positionCount.labels = response.data.labels
						// this.total = response.data.total
						this.positionsLoading = false
					}
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
