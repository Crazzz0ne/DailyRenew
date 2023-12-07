<template>
  <div>
    <router-view />
  </div>
</template>

<script>
import ReportingDashboard from './ReportingDashboard'
import axios from 'axios'

export default {
	name: 'ReportingApp',
	components: {
		ReportingDashboard
	},
	mounted () {

	},
	methods: {

	}
}
</script>

<style scoped>

</style>
