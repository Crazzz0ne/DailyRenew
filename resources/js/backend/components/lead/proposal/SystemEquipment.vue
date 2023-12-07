<template>
  <div class="row">
    <div class="col-md-6 col-sm-12">
      <div
        v-if="canEdit"
        class="form-group"
      >
        <MazSelect
          v-model="selectedSystem.modules_id"
          :placeholder="'Modules'"
          :options="modules"
          :debounce="true"
          :disabled="!canEdit"
          @input="$emit('updateModules', $event)"
        />
      </div>
      <div
        v-else
        class="form-group"
      >
        <template v-if="selectedSystem.inverter_id">
          <h5>Solar Module</h5>
          <span>{{ showModule }} </span>
        </template>
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <div
        v-if="canEdit"
        class="form-group"
      >
        <MazSelect
          v-model="selectedSystem.inverter_id"
          :placeholder="'Inverter'"
          :options="inverters"
          :debounce="true"
          :disabled="!canEdit"
          @input="$emit('updateInverter', $event)"
        />
      </div>
      <div
        v-else
        class="form-group"
      >
        <template v-if="selectedSystem.inverter_id">
          <h5>Inverter</h5>
          <span>{{ showInverter }} </span>
        </template>
      </div>
    </div>

    <div class="col-sm-12 col-md-6 my-5">
      <div
        v-if="canEdit"
        class="form-group pb-2"
      >
        <vue-slider
          v-model="selectedSystem.modules_count"
          :disabled="!canEdit"
          :marks="modulesCountArray"
          @drag-end="$emit('updateModulesCount', selectedSystem.modules_count)"
        />
      </div>
      <div class="form-group">
        <h5>Solar Panels</h5>
        {{ selectedSystem.modules_count }}
      </div>
    </div>
  </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/antd.css'

export default {
	name: 'SystemEquipment',
	components: {
		VueSlider
	},

	props: [

		'canEdit',
		'epcSystems',
		'modules',
		'inverters',
		'selectedSystem'

	],
	computed: {
		modulesCountArray () {
			return [0, 50, 100]
		},
		modulesArray () {
			const x = []
			$.each(this.modules, function (key, value) {
				x.push({ label: value.name, value: value.id })
				x.value = value.id
			})
			return x
		},

		inverterArray () {
			const x = []
			$.each(this.inverters, function (key, value) {
				x.push({ label: value.name, value: value.id })
				x.value = value.id
			})
			return x
		},
		showInverter () {
			if (this.inverters.length) {
				return this.inverters.find(d => d.value === this.selectedSystem.inverter_id).label
			}
		},
		showModule () {
			if (this.modules.length) {
				return this.modules.find(d => d.value === this.selectedSystem.modules_id).label
			}
		}
	}
}
</script>

<style scoped>

</style>
