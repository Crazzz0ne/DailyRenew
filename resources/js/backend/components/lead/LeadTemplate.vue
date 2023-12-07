<template>
  <div class="card">
    <div class="card-header">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="col-md-6 col-sm-12 pt-2">
          <h2 class="display-5 text-white">
            New Lead
          </h2>
        </div>
        <div class="col-md-6 col-sm-12" />
      </div>
    </div>
    <div class="card-body">
      <div class="container">
        <div class="row">
          <div class="col-md-6 col-sm-12" />
        </div>
        <div class="col-md-6 col-sm-12" />
        <div class="col-md-6 col-sm-12" />
      </div>
      <div class="d-flex justify-content-end">
        <div class="col-4">
          <router-link
            to="/dashboard/lead/create"
            data-toggle="tooltip"
            title=""
            class="btn btn-third ml-1 float-right"
          >
            <span class="pr-1">Submit Lead</span>
            <i class="fas fa-plus-circle" />
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'LeadTemplate'
}
</script>

<style scoped>

</style>
