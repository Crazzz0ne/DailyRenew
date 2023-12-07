<template>
  <div>
    <div class="row my-3">
      <div class="col-md-8 col-sm-12">
        <h4>Up Coming Time On</h4>
        <MazList
          class="scrollable-list "
          :style="'max-height:500px;'"
        >
          <MazListItem
            v-for="(timeOff, index) in currentRequests"
            :key="index"
          >
            <div class="py-1">
              {{ $date(timeOff.start).format('ddd, MMMM D') }}
              {{ $date(timeOff.start).format('h:mm a') }} -
              {{ $date(timeOff.end).format('h:mm a') }}
              <MazBtn
                class="float-right "
                color="danger"
                icon-name="delete"
                size="sm"
                fab
                @click="deleteTimeOff(timeOff.id)"
              />
            </div>
          </MazListItem>
        </MazList>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'AvailableList',
	props: ['userId'],
	data () {
		return {
			endTime: '2021-08-20 8:00 pm',
			startTime: '2021-08-20 9:00 am',
			currentRequest: null,
			selectedDate: null,
			timeOffRequest: {},
			currentRequests: [],
			submitting: false,
			disabledHours: [1, 2, 3, 4, 5, 6, 7]
		}
	},
	created () {
		this.getCurrentRequests()
	},
	methods: {
		getCurrentRequests () {
			axios.get(`/api/salesflow/user/${this.userId}/availability`)
				.then((response) => {
					this.currentRequests = response.data.data
				})
		}
	}
}
</script>

<style scoped>

</style>
