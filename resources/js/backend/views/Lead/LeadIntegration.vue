<template>
  <div class="card">
    <div class="card-header">
      <strong>Integrations {{ }}</strong>
    </div>
    <div class="card-body">
      <div class="row justify-content-between">
        <div class="col-lg-6 col-sm-12">
          <div class="form-group">
            <div class="form-group">
              <label for="language">Language</label>
              <select
                id="language"
                v-model="currentLead.customer.language"
                name="language"
                class="text-capitalize form-control"
                autofocus="autofocus"
              >
                <option
                  selected
                  value="English"
                >
                  English
                </option>
                <option value="English">
                  Spanish
                </option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-sm-12">
          <div class="form-group">
            <label for="name">Name</label>
            <input
              id="name"
              v-model="currentLead.customer.name"
              type="text"
              name="name"
              placeholder="John Smith"
              maxlength="80"
              required="required"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="address">Address</label>
            <input
              id="address"
              v-model="currentLead.customer.address.street_address"
              type="text"
              name="address"
              placeholder="123 Main"
              maxlength="80"
              required="required"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="city">City</label>
            <input
              id="city"
              v-model="currentLead.customer.address.city"
              type="text"
              name="city"
              placeholder="SunnyTown"
              maxlength="80"
              required="required"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-lg-6 col-sm12">
          <div class="form-group">
            <label for="cell_phone">Cell phone</label>
            <input
              id="cell_phone"
              v-model="currentLead.customer.cell_phone"
              type="text"
              name="cell_phone"
              placeholder="888-555-1234"
              maxlength="12"
              autofocus="autofocus"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-lg-6 col-sm12">
          <div class="form-group">
            <label for="home_phone">Home phone</label>
            <input
              id="home_phone"
              v-model="currentLead.customer.home_phone"
              type="text"
              name="home_phone"
              placeholder="888-867-5309"
              maxlength="12"
              autofocus="autofocus"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-lg-6 col-sm12">
          <div class="form-group">
            <label for="email">Email</label>
            <input
              id="email"
              v-model="currentLead.customer.email"
              type="text"
              name="email"
              placeholder="btk@funtime.org"
              maxlength="12"
              autofocus="autofocus"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-12">
          <hr class="divider">
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="address">Power Company</label>
            <input
              id="power_company"
              v-model="currentLead.lead.power_company"
              type="text"
              name="power_company"
              placeholder="Edison"
              maxlength="80"
              required="required"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="city">Rate Plan</label>
            <input
              id="rate_plan"
              v-model="currentLead.lead.rate_plan"
              type="text"
              name="rate_plan"
              placeholder="money"
              maxlength="80"
              required="required"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="system_size">System Size</label>
            <input
              id="system_size"
              v-model="currentLead.lead.system_size"
              type="text"
              name="system_size"
              placeholder="10,000"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="follow_up_date">Date of Follow Up</label>
            <input
              id="follow_up_date"
              v-model="currentLead.lead.follow_up_date"
              type="text"
              name="follow_up_date"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label for="upload">Upload</label>
            <input
              id="upload"
              v-model="currentLead.lead.uploads"
              type="text"
              name="follow_up_date"
              class="form-control"
            >
          </div>
        </div>
        <div class="col-12">
          <div class="form-group">
            <label for="notes">Notes</label>
            <textarea
              id="notes"
              v-model="currentLead.lead.notes"
              type="text"
              name="follow_up_date"
              class="form-control"
            />
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="form-group">
            <div class="form-check ml-2 mt-2">
              <input
                id="system_built"
                v-model="currentLead.lead.system_built"
                class="form-check-input"
                type="checkbox"
              >
              <label
                class="form-check-label"
                for="system_built"
              >
                System Built
              </label>
            </div>
          </div>
          <div class="form-group float-right">
            <button
              type="submit"
              class="btn btn-success btn-lg pull-right"
              @click="submitLead($route.params.leadId)"
            >
              Submint
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapFields } from 'vuex-map-fields'

export default {
	name: 'LeadIntegration',
	computed: {
		...mapFields([
			'Leads.currentLead'
		])
	},

	mounted () {
		this.loadLead()
	},
	methods: {
		loadLead: function () {
			if (this.currentLead !== null) {
				axios.get(`/api/salesflow/lead/${this.$route.params.leadId}`, {})
					.then(function (response) {
						this.$store.state.currentLead = response.data
						this.$store.state.customer = response.data.customer
						console.log(response.data)
					})
					.catch(function (error) {
						console.log(error)
					})
			}
		},

		submitLead: function (leadId) {
			const data = {
				lead: this.currentLead.lead,
				customer: this.currentLead.customer,
				user: this.currentLead.user
			}

			// console.log(JSON.stringify(data));

			axios.patch(`/api/salesflow/lead/${leadId}`, data)
				.then((response) => {
					this.$router.push({ path: '/dashboard/lead/' })
				})
				.catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>
    .divider {
        border-top: 3px double #8c8b8b;
    }
    #system_built {
        transform: scale(2);
    }
    .form-check {

    }
</style>
