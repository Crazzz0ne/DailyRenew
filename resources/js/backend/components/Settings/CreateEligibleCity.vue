<template>
  <div class="d-flex">
    <MazInput
      v-model="city"
      class="mb-2"
      :placeholder="'New City'"
      @keydown.enter="createNewCity()"
    />
    <MazBtn
      class="ml-2"
      color="success"
      icon-name="grading"
      size="sm"
      fab
      @click="createNewCity()"
    />
  </div>
</template>

<script>
export default {
	name: 'CreateEligibleCity',
	props: {
		officeId: String
	},
	data () {
		return {
			city: null
		}
	},
	created () {

	},
	methods: {
		createNewCity () {
			const data = { city: this.city }
			axios.post('/api/settings/eligible-city', { city: this.city })
				.then((response) => {
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
.center-box {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
