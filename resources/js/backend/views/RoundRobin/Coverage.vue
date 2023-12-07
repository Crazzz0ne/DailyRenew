<template>
  <div>
    <city-list />
    <div
      v-if="coverage"
      class="container"
    >
      <div class="row">
        <div
          v-for="(user, index) in userList"
          :key="index"
          class="col-6"
        >
          <div class="card">
            <div class="card-header">
              {{ user.name }}
            </div>
            <div class="card-body">
              <h5>Cities</h5>
              {{ cityListString(user.cities) }}
            </div>
            <!--                    <div class="row">-->
            <!--                        <div class="col-sm-6 col-md-2 text-center"-->
            <!--                             style="display: flex;-->
            <!--                         justify-content: center;"-->
            <!--                             v-for="user in office.userList">-->
            <!--                            <div class="text-center">-->

            <!--                                <button @click="deleteUser(user, office.officeId)" type="button" class="close"-->
            <!--                                        aria-label="Close">-->
            <!--                                    <span aria-hidden="true">&times;</span>-->
            <!--                                </button>-->
            <!--                                <MazAvatar-->
            <!--                                    style="margin: auto;"-->
            <!--                                    :size="avatarSize"-->
            <!--                                    :src="user.gravatar"-->
            <!--                                />-->
            <!--                                {{ user.name }}-->
            <!--                            </div>-->
            <!--                        </div>-->

            <!--                    </div>-->
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <MazLoader />
    </div>
  </div>
</template>

<script>

import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/antd.css'
import axios from 'axios'
import CityList from '../../components/RoundRobin/CityList'

export default {
	name: 'Coverage',
	components: {
		CityList
	},
	data () {
		return {
			repSortOptions: [
				{ label: 'Current Appointments', value: 'currentAppointments' },
				{ label: 'Closing Percent', value: 'closingPercent' }
			],
			repSortBy: 'currentAppointments',
			coverage: null,
			userList: null,
			closePercent: 60,
			slider: [0, 50, 75, 100]
		}
	},
	computed: {
		cityPriority () {
			let userList = []

			userList = Object.values(this.userList)

			switch (this.repSortBy) {
			case 'currentAppointments':
				return userList.sort((a, b) => {
					return a.currentAppointments - b.currentAppointments
				})
				break
			case 'closingPercent':
				return userList.sort((a, b) => {
					return b.closingPercent - a.closingPercent
				})
				break
			}
		},
		cityCount () {
			let userList = []
			userList = Object.values(this.userList)
			const cityList = []
			const closePercent = this.closePercent
			console.log(closePercent)
			if (userList.length) {
				userList.forEach(function (key, value) {
					if (key.closingPercent >= closePercent) {
						const cityArray = Object.values(key.cities)
						cityArray.forEach(function (city) {
							cityList.push(city)
							// if (cityList.indexOf(city) === -1) {
							//     cityList.push({city: city, count: 1});
							// }else {
							//     console.log('weee')
							//     cityList[cityList.indexOf(city)].count= 5;
							// }
							// console.log(cityList.indexOf(city))
							// console.log(city, 'city')
						})
					}
				})
				return cityList
			}
		}
	},
	created () {
		this.getCoverage()
	},
	methods: {
		getCoverage () {
			axios.get('/api/round-robin/coverage')
				.then((response) => {
					this.coverage = response.data.ListOfAll
					this.userList = response.data.UserCity
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		cityListString (array) {
			console.log(array, 'array')
			if (Array.isArray(array)) {
				return array.join(', ')
			} else {
				return []
			}
		}
	}
}
</script>

<style scoped>

</style>
