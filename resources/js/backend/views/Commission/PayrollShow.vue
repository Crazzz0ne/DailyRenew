<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h3>Payroll</h3>
      </div>
      <div class="card-body">
        <div>
          <MazBtn
            :icon-name="'arrow_back'"
            fab
            @click="back"
          />
        </div>
        <div
          v-if="!loading"
          class="row justify-content-between py-3"
        >
          <div class="col-md-4 col-sm-12">
            <h5>{{ payroll.user.fullName }}</h5>
          </div>
          <div class="col-md-4 col-sm-12">
            <ul class="list-group">
              <li class="list-group-item list-group-item-dark">
                Period: {{ $date(payroll.created_at).format('M/D/YY') }}
              </li>
              <li class="list-group-item list-group-item-success">
                Total: ${{ payroll.amount.toLocaleString() }}
              </li>
            </ul>
          </div>
        </div>
        <div class="table-responsive p-2">
          <table
            v-if="!loading"
            class="table"
          >
            <thead>
              <tr>
                <th scope="col">
                  #
                </th>
                <th scope="col">
                  Type
                </th>
                <th class="col-3">
                  Date
                </th>
                <th scope="col-3">
                  Customer Name
                </th>
                <th scope="col">
                  Lead ID
                </th>
                <th scope="col">
                  Amount
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="recent in commissions">
                <th scope="row">
                  {{ recent.id }}<br>
                  <MazBtn
                    :icon-name="'visibility'"
                    :size="'mini'"
                    fab
                    @click="openLead(recent.lead_id)"
                  />
                </th>
                <td>{{ recent.type }}</td>
                <td class="col-3">
                  {{ $date(recent.created_at).format('M/D/YY h:mm a') }}
                </td>
                <td>{{ recent.customer }}</td>
                <td>{{ recent.lead_id }}</td>
                <td>${{ recent.amount }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'PayrollShow',
	components: {},

	data () {
		return {
			loading: true,
			payroll: {},
			commissions: {}
		}
	},
	created () {
		this.fetchUsers().then(() => {
			this.getTotal()
		})
	},
	computed: {
		...mapGetters([
			'getUser'
		])
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		openLead: function (id) {
			this.$router.push({ path: `/dashboard/lead/${id}` })
		},
		back: function () {
			this.$router.push({ path: '/dashboard/commission' })
		},

		getTotal () {
			axios.get(`/api/payroll/${this.$route.params.payrollId}`)
				.then((response) => {
					this.payroll = response.data.data
					this.commissions = this.payroll.commissionDetails
					this.loading = false
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
