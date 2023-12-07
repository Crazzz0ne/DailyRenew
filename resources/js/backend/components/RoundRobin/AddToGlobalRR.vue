<template>
  <div>
    <div class="d-flex">
      <MazSelect
        v-if="officeList"
        v-model="selectedOffice"
        :options="officeList"
        :size="'sm'"
        :placeholder="'Office'"

        search
        search-placeholder="office Search"
        color="info"
      >
        <i
          slot="icon-left"
          class="material-icons"
        >
          add_circle_outline
        </i>
      </MazSelect>
      <MazBtn
        :loading="submitting"
        class="ml-2"
        color="success"
        icon-name="grading"
        size="sm"
        fab
        @click="addOffice()"
      />
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'AddToGlobalRR',
	data () {
		return {
			userList: null,
			selectedUser: null,
			selectedOffice: null,
			officeList: null,
			labelLoaded: false,
			userLoaded: false,
			submitting: false
		}
	},
	mounted () {
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
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		addOffice () {
			this.submitting = true
			axios.post('/api/round-robin', { officeId: this.selectedOffice })
				.then((response) => {
					this.$emit('new-office')
					this.selectedOffice = null
					this.submitting = false
					console.log(response)
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
