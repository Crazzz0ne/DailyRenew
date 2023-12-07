<template>
  <div>
    <router-view />
  </div>
</template>

<script>

import CommissionDashboard from './CommissionDashboard'

export default {
	name: 'CommissionApp',
	components: {
		CommissionDashboard
	}
}
</script>

<style scoped>

</style>
