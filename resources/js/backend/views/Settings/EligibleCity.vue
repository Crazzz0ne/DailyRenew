<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        Manage Eligible Cities
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <create-eligible-city />
            <eligible-cities />
          </div>
          <div class="col-md-6 col-sm-12">
            <UserOfficeSelect
              :can-view-users="false"
              :can-view-offices="true"
              @officeChange="officeId = $event"
            />
            <city-select
              :office-id="officeId"
              @selected-cities="officeCities = $event"
            />
            <eligible-cities-by-office
              :all-cities="officeCities"
              :office-id="officeId"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import EligibleCities from '../../components/Settings/EligibleCities'
import CreateEligibleCity from '../../components/Settings/CreateEligibleCity'
import EligibleCitiesByOffice from '../../components/Settings/EligibleCitiesByOffice'
import UserOfficeSelect from '../../components/Selects/UserOfficeSelect'

export default {
	name: 'EligibleCity',
	components: { UserOfficeSelect, EligibleCitiesByOffice, CreateEligibleCity, EligibleCities },
	data () {
		return {
			officeCities: [],
			officeId: 1
		}
	}
}
</script>

<style scoped>

</style>
