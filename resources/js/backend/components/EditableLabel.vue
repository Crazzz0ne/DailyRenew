<template>
  <div v-click-outside="cancel">
    <input
      v-show="edit"
      ref="field"
      class="w-50"
      type="text"
      :value="valueLocal"
      @keyup.enter="processInputEvent"
    >
    <p
      v-show="!edit"
      @click="switchToEditMode"
    >
      {{ formattedString }}
    </p>
  </div>
</template>

<script>
export default {
	name: 'EditableLabel',
	directives: {
		focus: {
			inserted (el) {
				el.focus()
			}
		}
	},
	props: {
		value: { type: [String, Number], required: false, default: 0 },
		format: { type: String, required: false, default: 'usd' },
		editable: { type: Boolean, required: false, default: false }
	},
	data () {
		return {
			edit: false,
			valueLocal: this.value
		}
	},
	computed: {
		formattedString () {
			if (this.format === 'usd') { return '$' + this.value } else return this.value
		}
	},
	watch: {
		value: function () {
			this.valueLocal = this.value
		}
	},
	methods: {
		switchToEditMode () {
			if (!this.editable) { return }

			this.edit = true
			setTimeout(() => {
				// Timeout required to provide buffer period for UI to update, otherwise selection of text does not work. Seems fine at 100ms
				this.$refs.field.select()
			}, 10)
		},
		processInputEvent (event) {
			this.valueLocal = event.target.value
			this.edit = false
			this.$emit('input', this.valueLocal)
		},
		cancel () {
			this.edit = false
		}
	}
}
</script>

<style scoped>

</style>
