<template>
  <div>
    <router-view />
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import axios from 'axios'

import LeadDashboard from './OldLeadDashboard'

export default {
	name: 'LeadApp',
	components: {
		LeadDashboard
	},
	mounted () {
		// this.fetchUsers();
		// this.fetchLeads({type: 'user', id: '1'});
		// this.fetchLeads({type: 'office', id: '1'});
		// this.axios();
	},
	// computed: {
	//     ...mapGetters(["getUser",]),
	// },
	methods: {

	}
}
</script>

<style scoped>

</style>
