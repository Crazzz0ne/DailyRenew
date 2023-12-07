<template>
  <div>
    <div v-if="$can('administrate all logins')">
      You can edit posts.
    </div>
    <h2>Current time off requested</h2>
    <h4>Approved</h4>
    <div
      class="btn btn-primary float-right"
      @click="request"
    >
      Request Time off
    </div>
    <div
      v-if="$can('administrate all users')"
      class="btn btn-success"
      @click="approve"
    >
      Approve Days Off.
    </div>

    <ul>
      <li
        v-for="day in currentTimeOff"
        v-if="day.approved"
      >
        {{ $date(day.start).format('MMMM d, YYYY h:mm ')
        }} - {{ $date(day.end).format('h:mm A') }}
      </li>
    </ul>
    <h4>Not Approved</h4>
    <ul>
      <li
        v-for="day in currentTimeOff"
        v-if="!day.approved"
      >
        {{ $date(day.start).format('MMMM d, YYYY h:mm ')
        }} - {{ $date(day.end).format('h:mm A') }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
	name: 'TimeOff',
	data () {
		return {
			currentTimeOff: {}
		}
	},

	beforeMount () {
		this.getTimeOff(this.$route.params.calenderId)
	},
	methods: {
		getTimeOff: function () {
			axios.get('/api/salesflow/availability?user_id=12')
				.then((response) => {
					this.currentTimeOff = response.data.data
					console.log(this.currentTimeOff)
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		request: function () {
			return this.$router.push({ path: '/dashboard/calender/time-off/request' })
		},
		approve: function () {
			return this.$router.push({ path: '/dashboard/calender/time-off/approve' })
		}
	}

}
</script>

<style scoped>

</style>
