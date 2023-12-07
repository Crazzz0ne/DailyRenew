<template>
  <div class="container">
    <h1>Not Approved</h1>
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">
            Rep
          </th>
          <th scope="col">
            Date
          </th>
          <th scope="col">
            Approve?
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="re in timeOffRequests"
          v-if="!re.approved"
          :key="re.id"
        >
          <th>{{ re.user.full_name }} </th>
          <td>
            {{ $date(re.start).format('MMMM d, YYYY h:mm ')
            }} - {{ $date(re.end).format('h:mm A') }}
          </td>
          <td>
            <input
              v-model="re.approved"
              type="checkbox"
              @change="sendData(re)"
            >
          </td>
        </tr>
      </tbody>
    </table>

    <h1>Approved</h1>
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">
            Rep
          </th>
          <th scope="col">
            Date
          </th>
          <th scope="col">
            Approve?
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="re in timeOffRequests"
          v-if="re.approved"
          :key="re.id"
        >
          <th>{{ re.user.full_name }} </th>
          <td>
            {{ $date(re.start).format('MMMM d, YYYY h:mm ')
            }} - {{ $date(re.end).format('h:mm A') }}
          </td>
          <td>
            <input
              v-model="re.approved"
              type="checkbox"
              @change="sendData(re)"
            >
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
	name: 'ApproveTimeOff',
	data () {
		return {
			timeOffRequests: {}
		}
	},
	created () {
		this.getTimeOff()
	},
	methods: {
		getTimeOff: function () {
			axios.get('/api/salesflow/availability?type=office')
				.then((response) => {
					this.timeOffRequests = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		sendData: function (re) {
			console.log('test', re)
			axios.put(`/api/salesflow/availability/${re.id}`, re)
				.then((response) => {
					response.data
					console.log(response.data)
				})
				.catch(function (error) {
					if (re.approved) {
						re.approved = false
					} else {
						re.approved = true
					}
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
