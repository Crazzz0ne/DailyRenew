<template>
  <div
    class="checkbox-toggle"
    role="checkbox"
    tabindex="0"
    :aria-checked="toggled"
    @keydown="toggle"
    @click.stop="toggle"
  >
    <div
      class="checkbox-slide"
      :class="classes"
    >
      <div
        class="checkbox-switch"
        :class="classes"
      />
    </div>
    <div
      v-show="showLabels"
      class="checkbox-label"
      v-html="label"
    />
  </div>
</template>

<script>
export default {
	name: 'CheckBoxToggle',

	props: {
		disabled: {
			type: Boolean,
			default: false
		},

		value: {
			type: Boolean,
			default: false
		},

		showLabels: {
			type: Boolean,
			default: false
		},

		labelChecked: {
			type: String,
			default: ''
		},

		labelUnchecked: {
			type: String,
			default: ''
		}
	},

	data () {
		return {
			toggled: this.value
		}
	},
	computed: {
		classes: function () {
			return {
				checked: this.toggled,
				unchecked: !this.toggled,
				disabled: this.disabled
			}
		},

		label: function () {
			return this.toggled && this.showLabels
				? this.labelChecked
				: this.labelUnchecked
		}
	},

	methods: {
		toggle: function (e) {
			if (this.disabled || e.keyCode === 9) { // not if disabled or tab is pressed
				e.stop()
			}

			this.toggled = !this.toggled

			this.$emit('input', this.toggled)
		}
	}
}
</script>

<style scoped>

</style>
