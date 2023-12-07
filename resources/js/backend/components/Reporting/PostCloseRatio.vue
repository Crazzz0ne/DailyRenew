<template>
  <div class="row pt-5">
    <div class="col-12">
      <div class="text-center">
        <h5>Post Close</h5>
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <template v-if="!pieClosedLoading && pieClosedTotal">
        <PieChart
          v-if="!pieClosedLoading"
          :chartdata="pieClosedRatio"
          :options="options"
          :style="`height: 400px`"
        />
      </template>
      <template v-else-if="total == 0">
        <div class="text-center">
          No Data
        </div>
      </template>
      <template v-else>
        <MazLoader />
      </template>
    </div>
    <div class="col-md-6 col-sm-12">
      <div>
        <div class="text-center pt-2">
          <p>{{ dateRangeFormatted }}</p>
        </div>
        <h5>{{ selectedUserName }}</h5>
        <p><strong>{{ selectedOfficeName }}</strong></p>

        <p>
          Total {{ pieClosedTotal }}
        </p>
      </div>

      <div
        v-if="!pieClosedLoading"
        class="row py-3"
      >
        <div
          v-for="value in pieClosedValues"
          class="col-sm-6 py-1 col-md-4"
        >
          {{ value.label }}<br> <strong>{{ value.percent }}%</strong>
        </div>
      </div>
      <div>
        <p>Based off of closes within date range</p>
      </div>
      <div>
        <MazBtn
          class="maz-mr-2 maz-mb-2"
          @click="detailsModel = true"
        >
          Details
        </MazBtn>
        <MazDialog
          v-model="detailsModel"
        >
          <div slot="title">
            Details
          </div>
          <div>
            <MazSelect
              v-model="selectedType"
              :options="detailsOptions"
            />
          </div>
          <div>
            <div
              v-for="d in selectedDetail"
              class="pt-3"
            >
              LeadId: {{ d.leadId }}
              <MazBtn
                :size="'mini'"
                fab
                @click="openLead(d.leadId)"
              >
                <i class="fa fa-eye" />
              </MazBtn>
              <br>
              Customer Name: {{ d.customerName }}
              <br>
            </div>
          </div>
        </MazDialog>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PieChart from '../chart/PieChart'

export default {
	name: 'PostCloseRatio',
	components: { PieChart },
	props: {
		selectedUser: Number,
		selectedOffice: Number,
		selectedRegion: Number,
		dateRange: Object,
		dateRangeFormatted: String,
		selectedUserName: String,
		selectedOfficeName: String
	},
	data () {
		return {
			details: {},
			selectedType: null,
			detailsOptions: [
				{ label: 'Select One', value: null },
				{ label: 'Pending', value: 'pending' },
				{ label: 'JIJ', value: 'jij' },
				{ label: 'Canceled', value: 'canceled' },
				{ label: 'Installed', value: 'install' }
			],
			detailsModel: false,
			pieTotal: 69,
			creditOnly: false,
			pieClosedTotal: null,
			pieClosedRatio: {
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
				labels: []

			},
			pieClosedLoading: true,
			pieLoading: true,
			positionsLoading: true,
			options: {
				maintainAspectRatio: false,
				animation: {
					duration: 0
				},
				hover: {
					animationDuration: 0
				}
			},

			total: 0,
			labelLoaded: false
		}
	},
	computed: {
		selectedDetail () {
			switch (this.selectedType) {
			case 'installed':
				return this.details.installed
			case 'jij':
				return this.details.jij
			case 'canceled':
				return this.details.canceled
			case 'pending':
				return this.details.pending
			default:
				return null
			}
		},
		pieClosedValues () {
			let i = 0
			const array = []
			const labels = this.pieClosedRatio.labels
			const total = this.pieClosedTotal
			if (!this.loading && this.pieClosedRatio.labels && this.pieClosedRatio.labels) {
				$.each(this.pieClosedRatio.datasets[0].data, function (key, value) {
					const percent = (value / total) * 100
					const temp = {
						label: labels[i],
						percent: percent.toFixed(1)
					}
					i++
					array.push(temp)
				})
			}

			return array
		}
	},
	watch: {
		creditOnly: function (newVal, oldVal) {
			this.getPieClosed()
		},
		selectedOffice: function (newVal, oldVal) {
			this.getPieClosed()
		},
		selectedRegion: function (newVal, oldVal) {
			this.getPieClosed()
		},
		dateRange: function (newVal, oldVal) {
			this.getPieClosed()
		},
		selectedUser: function (newVal, oldVal) {
			this.getPieClosed()
		}
	},
	created () {
		this.getPieClosed()
	},
	methods: {
		openLead: function (id) {
			const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
			window.open(routeData.href, '_blank')
		},
		getPieClosed () {
			this.pieClosedLoading = true
			let urlss = null

			urlss = '/api/salesflow/reporting/closed-install-ratio'

			axios.get(urlss,
				{
					params: {
						dateRange: this.dateRange,
						creditOnly: this.creditOnly,
						region_id: this.selectedRegion,
						user_id: this.selectedUser,
						office_id: this.selectedOffice

					}
				})
				.then((response) => {
					this.details = response.data.details
					this.pieClosedRatio.datasets[0] = response.data.datasets
					this.pieClosedRatio.labels = response.data.labels
					this.pieClosedTotal = response.data.total
					this.pieClosedLoading = false
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
