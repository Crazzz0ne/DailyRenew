<template>
  <div class="container">
    <div class="row justify-content-between pt-3">
      <div class="col justify-content-start">
        <div class="float-left">
          <MazBtn
            :loading="buttonLoading"
            :icon-name="'arrow_back'"
            fab
            @click="getStatus(2)"
          />
        </div>
      </div>
      <div class="col-4">
        <div class="text-center">
          {{ rangeStart }} - {{ rangeEnd }}
        </div>
      </div>
      <div class="col justify-content-end">
        <div class="float-right">
          <MazBtn
            :loading="buttonLoading"
            :icon-name="'arrow_forward'"
            fab
            @click="getStatus(1)"
          />
        </div>
      </div>
    </div>
    <div>
      <div class="text-center">
        <h5>Sits By SP1</h5>
      </div>
      <div class="row justify-content-center">
        <div class="w-25 mx-auto">
          <div
            v-if="rawSits"
            class="table"
          >
            <thead>
              <tr>
                <th scope="col">
                  Name
                </th>
                <th scope="col">
                  Sits
                </th>
                <th scope="col">
                  $$
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="data in tableData">
                <td>{{ data.name }}</td>
                <td>{{ data.count }}</td>
                <td>{{ data.total }}</td>
              </tr>
            </tbody>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'SitByUser',
	data () {
		return {
			buttonLoading: false,
			rangeStart: null,
			rangeEnd: null,
			rawSits: null
		}
	},
	computed: {

		tableData () {
			const barf = []
			$.each(this.rawSits, function (skey, value) {
				const TempTotal = value * 50
				barf.push({
					name: skey,
					count: value,
					total: TempTotal
				})
				console.log(skey, value)
			})
			return barf
		}
	},
	mounted () {
		this.getStatus(0)
	},
	methods: {
		getStatus (direction) {
			this.buttonLoading = true
			let params = null
			if (direction === 0) {
				params = null
			} else if (direction === 1) {
				params = {
					start: this.rangeStart,
					forward: true
				}
			} else if (direction === 2) {
				params = {
					start: this.rangeStart,
					backwards: true
				}
			}
			axios.get('/api/salesflow/reporting/sit-by-user', { params })
				.then((response) => {
					this.rawSits = response.data.data
					this.rangeStart = response.data.dateRange.start
					this.rangeEnd = response.data.dateRange.end

					this.buttonLoading = false
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
