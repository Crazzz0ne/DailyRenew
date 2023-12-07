<template>
  <div class="">
    <div
      v-if="editing"
      class=""
    >
      <div class="form-group">
        <label>Size</label>
        <input
          v-model="proposal.system_size"
          type="number"
          name="system_size"
          min="1"
          step="100"
          class="form-control"
          :disabled="!editing"
        >
      </div>
    </div>
    <div
      v-else
      class=""
    >
      <div class="form-group">
        <h5>Size</h5>
        <span>{{ proposal.system_size }} </span>
      </div>
    </div>
    <div
      v-show="market !== 'antelope valley'"
      class=""
    >
      <div
        v-if="editing"
        class="form-group"
      >
        <label>Solar Rate</label>
        <input
          v-model="proposal.solar_rate"
          type="number"
          placeholder="25"
          maxlength="2"
          class="form-control"
          min="1"
          step="1"
          :disabled="!editing"
        >
      </div>
      <div v-else>
        <h5>Solar Rate</h5>
        <p>{{ proposal.solar_rate }}</p>
      </div>
    </div>
    <div class="">
      <div
        v-if="editing"
        class="form-group"
      >
        <label>Monthly Payment</label>
        <input
          v-model="proposal.monthly_payment"
          type="number"
          placeholder="250"
          maxlength="4"
          min="1"
          step="1"
          class="form-control"
          :disabled="!editing"
        >
      </div>
      <div v-else>
        <h5>Monthly Payment</h5>
        <p>{{ proposal.monthly_payment }}</p>
      </div>
    </div>
    <div class="">
      <div
        v-if="editing"
        class="form-group"
      >
        <label>Offset</label>
        <input
          v-model="proposal.offset"
          type="text"
          placeholder="110"
          maxlength="4"
          class="form-control"
          :disabled="!editing"
        >
      </div>
      <div v-else>
        <h5>Offset</h5>
        {{ proposal.offset }}
      </div>
    </div>
    <div class="">
      <div
        v-if="editing"
        class="form-group"
      >
        <label for="system">System</label>
        <select
          id="system"
          v-model="proposal.system"
          name="proposal"
          class="text-capitalize form-control"
          :disabled="!editing"
        >
          <option :value="null">
            Select One
          </option>
          <option :value="'420-watt Q-Cells '">
            420-watt Q-Cells
          </option>
          <option :value="'340-watt Q-Cells'">
            340-watt Q-Cells
          </option>
          <option :value="'Premium (+$.30/w): Solaria 430-watt Panels + SolarEdge'">
            Premium (+$.30/w): Solaria 430-watt Panels + SolarEdge
          </option>
        </select>
      </div>
      <div v-else>
        <h5>System</h5>
        <p>{{ proposal.system }}</p>
      </div>
    </div>

    <div class="">
      <div
        v-if="editing"
        class="form-group"
      >
        <label>Adders</label>
        <input
          v-model="proposal.adders"
          type="text"
          placeholder="Ground Mount, Remove existing solar, EV Charger"
          maxlength="240"
          class="form-control"
          :disabled="!editing"
        >
      </div>
      <div v-else-if="proposal.adders">
        <h5>Adders</h5>
        <p>{{ proposal.adders }}</p>
      </div>
    </div>
    <div class="">
      <div
        v-if="editing"
        class="form-group"
      >
        <label>Credit Score</label>
        <select
          v-model="proposal.credit_score"
          name="credit score"
          class="text-capitalize form-control"
          :disabled="!editing"
        >
          <option :value="''">
            Select Score
          </option>
          <option :value="'+650'">
            +650
          </option>
          <option :value="'600-649'">
            600-649
          </option>
          <option :value="'-600'">
            -600
          </option>
        </select>
      </div>
      <div v-else-if="$can('administrate lead')">
        <h5>Credit Score</h5>
        {{ system.credit_score }}
      </div>
    </div>
    <div
      v-if="canStartProposal"
      class=""
    >
      <MazBtn
        class="btn btn-success"
        :loading="requestingProposal"
        @click="startProposal"
      >
        Request Proposal
      </MazBtn>
    </div>
  </div>
</template>

<script>
import 'dayjs/locale/es'
import axios from 'axios'
export default {
	name: 'OldRequestedSystem',
	props: {
		editing: Boolean,
		proposal: Object,
		lead: Object,
		canStartProposal: Boolean,
		requestingProposal: Boolean,
		market: String

	},
	data () {
		return {}
	},
	computed: {

	},
	methods: {
		startProposal () {
			this.$emit('start', true)
		}
	}
}
</script>

<style scoped>

</style>
