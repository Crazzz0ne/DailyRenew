<template>
  <div class="card">
    <div class="card-header">
      <h4>Round Robin</h4>
    </div>
    <div class="card-body">
      <div class="float-right">
        <MazBtn
          @click="openCoverage()"
        >
          Coverage
        </MazBtn>
      </div>
      <h5>Add office</h5>
      <AddToGlobalRR
        @new-office="callCenterRR++"
      />
      <div class="py-2">
        <h5>Call Center Round Robin</h5>
        <CallCenterList
          :draw="callCenterRR"
        />
      </div>
    </div>
  </div>
</template>

<script>
import CallCenterList from '../../components/RoundRobin/CallCenterList'
import AddToGlobalRR from '../../components/RoundRobin/AddToGlobalRR'

export default {
	name: 'RoundRobinDashboard',
	components: { AddToGlobalRR, CallCenterList },
	data () {
		return {
			callCenterRR: 0
		}
	},
	methods: {
		incermentRRCount () {
			this.callCenterRR++
		},
		openCoverage () {
			this.$router.push({ path: '/dashboard/round-robin/coverage' })
		}
	}
}
</script>

<style scoped>

</style>
