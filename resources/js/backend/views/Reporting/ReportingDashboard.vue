<template>
  <div class="card">
    <div class="card-header">
      <h2>Reporting</h2>
    </div>
    <div class="card-body">
      <Charts />
    </div>
  </div>
</template>

<script>
import Charts from '../../components/Reporting/Charts'
export default {
	name: 'ReportingDashboard',
	components: { Charts }

}
</script>

<style scoped>

</style>
