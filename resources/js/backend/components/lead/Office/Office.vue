<template>
  <div>
    <MazSelect
      v-model="selectedOffice"
      :options="officeList"
      :size="'sm'"
      :placeholder="'Office'"
      @input="changeOffice"
    />
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'Office',
	props: {
		officeId: Number,
		leadId: Number
	},
	data: function () {
		return {
			officeList: [],
			selectedOffice: 1
		}
	},
	created () {
		this.getOffice()
	},
	methods: {
		getOffice () {
			axios.get('/api/office')
				.then((response) => {
					const officeList = response.data.data
					const payload = []
					$.each(officeList, function (key, value) {
						const obj = {
							label: value.name,
							value: value.id
						}
						payload.push(obj)
					})
					this.officeList = payload
					this.labelLoaded = true
					this.selectedOffice = this.officeId
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		changeOffice () {
			const payload = {
				office_id: this.selectedOffice
			}
			axios.put(`/api/salesflow/lead/${this.leadId}`, payload)
				.then((response) => {
					this.$emit('officeChange', this.selectedOffice)
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
