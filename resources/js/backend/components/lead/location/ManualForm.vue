<template>
  <div class="container">
    <form>
      <div class="row">
        <div class="col-12">
          <div class="form-group">
            <label>Address</label>
            <input
              type="text"
              class="form-control"
              :value="address.street_address"
              @keyup="updateCustomerStreetAddress($event)"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label>City</label>
            <input
              v-model="address.city"
              type="text"
              class="form-control"
              @keyup="updateCustomerCity($event)"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label>Zipcode</label>
            <input
              v-model="address.zip"
              type="text"
              class="form-control"
              @keyup="updateCustomerZip($event)"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label>State</label>
            <input
              v-model="address.state"
              type="text"
              class="form-control"
              @keyup="updateCustomerState($event)"
            >
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>

export default {
	name: 'ManualForm',
	data () {
		return {
			address: {}

		}
	},
	computed: {

		streetAddress () {
			return this.getLead.customer.address.street_address
		}
	},
	methods: {
		updateCustomerAddress () {
			this.$emit('place', this.address)
		},

		updateCustomerStreetAddress: function (event) {
			this.address.street_address = event.target.value

			this.$emit('place', this.address)
		},
		updateCustomerCity: function (event) {
			this.address.city = event.target.value

			this.$emit('place', this.address)
		},
		updateCustomerState: function (event) {
			this.address.state = event.target.value

			this.$emit('place', this.address)
		},
		updateCustomerZip: function (event) {
			this.address.zip = event.target.value

			this.$emit('place', this.address)
		}
	}
}
</script>

<style scoped>

</style>
