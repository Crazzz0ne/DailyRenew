<template>
  <div>
    <div class="form-group">
      <label for="reason">Reason</label>
      <select
        id="reason"
        v-model="reason"
        name="creditStatus"
        class="text-capitalize form-control"
      >
        <option
          class="text-capitalize"
          value="null"
        >
          Select Why
        </option>
        <option
          class="text-capitalize"
          value="dark door"
        >
          Dark Door
        </option>
        <option
          class="text-capitalize"
          value="lost to comp"
        >
          Another Solar Company
        </option>
        <option
          class="text-capitalize"
          value="bad credit"
        >
          Bad Credit
        </option>
        <option
          class="text-capitalize"
          value="canceled appointment"
        >
          Canceled Appointment
        </option>
        <!--                <option class="text-capitalize" value="installed">Installed</option>-->
        <!--                <option class="text-capitalize" value="lost">Lost</option>-->
      </select>
    </div>
    <Note
      :is-new-lead="false"
      :lead-id="leadId"
      :user-id="userId"
    />
  </div>
</template>

<script>
import * as dayjs from 'dayjs'
import { mapActions, mapState, mapGetters } from 'vuex'
import Note from './note/Note'

export default {
	name: 'Disposition',
	components: { Note },
	props: {
		status: String,
		userId: Number,
		leadId: Number

	},
	data () {
		return {
			reason: null
		}
	},
	computed: {

	},
	mounted () {

	},
	methods: {

	},
	submit: function () {
		this.$emit('submit')
	}

}
</script>

<style scoped>

</style>
