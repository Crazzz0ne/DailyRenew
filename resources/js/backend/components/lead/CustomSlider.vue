<template>
  <vue-slider
    v-if="enabled"
    v-model="localValue"
    :value="value"
    :max="max"
    @click.native="triggerEvent"
    @drag-end="triggerEvent"
  />
  <div v-else>
    <p class="text-center">
      {{ value }}
    </p>
  </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/antd.css'

export default {
	name: 'CustomSlider',
	components: {
		VueSlider
	},
	props: {
		max: { type: Number, required: false, default: 100 },
		enabled: { type: Boolean, required: false, default: true },
		value: { type: Number, required: false, default: 0 }
	},
	data () {
		return {
			localValue: this.value
		}
	},
	watch: {
		value ($new, $old) {
			console.log($old, $new)
			this.localValue = $new
		}
	},
	methods: {
		triggerEvent () {
			this.$emit('input', this.localValue)
		}
	}
}
</script>
