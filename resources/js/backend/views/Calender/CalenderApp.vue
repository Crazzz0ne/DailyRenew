<template>
  <div>
    <router-view />
  </div>
</template>

<script>
import Calender from './Calender'
import axios from 'axios'

export default {
	name: 'CalenderApp',
	components: {
		Calender
	},
	mounted () {

	},
	methods: {

	}
}
</script>

<style scoped>

</style>
