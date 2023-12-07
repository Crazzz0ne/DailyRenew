<template>
  <div
    v-if="$can('view lead')"
    class="container"
  >
    <div class="card">
      <div class="card-header">
        <h1>Commissions</h1>
      </div>

      <div class="card-body">
        <div class="row justify-content-between pb-4">
          <div class="col">
            <strong>Day:</strong> ${{ yourDtd }}
          </div>
          <div class="col">
            <strong>Week:</strong> ${{ yourWtd }}
          </div>
          <div class="col">
            <strong>Month:</strong> ${{ yourMtd }}
          </div>
          <div class="col">
            <strong>YTD:</strong> ${{ yourYtd }}
          </div>
        </div>

        <recent-commission />
        <index-payroll />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'
import RecentCommission from '../../components/Commission/RecentCommission'
import IndexPayroll from '../../components/Commission/IndexPayroll'

export default {
	name: 'CommissionDashboard',
	components: { IndexPayroll, RecentCommission },

	data () {
		return {
			yourYtd: 0,
			yourMtd: 0,
			yourWtd: 0,
			yourDtd: 0
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		])
	},
	created () {
		this.fetchUsers().then(() => {
			this.getTotal()
		})
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		openLead: function (id) {
			this.$router.push({ path: `/dashboard/lead/${id}` })
		},

		getTotal () {
			axios.get(`/api/commission/total/rep/${this.getUser.data.id}`)
				.then((response) => {
					this.yourYtd = response.data.data.ytd

					this.yourWtd = response.data.data.wtd

					this.yourMtd = response.data.data.mtd

					this.yourDtd = response.data.data.dtd
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
