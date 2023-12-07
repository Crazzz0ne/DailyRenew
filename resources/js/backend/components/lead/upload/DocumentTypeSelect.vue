<template>
  <MazSelect
    v-model="value"
    :options="options"
    placeholder="Select Upload Type"
    class="mb-2"
    @input="updateSelection"
  />
</template>

<script>
import { MazSelect } from 'maz-ui'

export default {
	name: 'DocumentTypeSelect',
	components: { MazSelect },
	props: [
		'selected'
	],
	data () {
		return {
			value: null,
			options: []
		}
	},
	computed: {
		canEditProposalsAndAdministrateLeads () {
			return this.$can('administrate leads') || this.$can('edit proposal') || this.$can('accept credit runner')
		},
		canEditIntegrations () {
			return this.$can('edit integrations status')
		},
		canCloseDeal () {
			return this.$can('create close date')
		}
	},
	watch: {
		selected () {
			this.value = this.selected
		}
	},
	created () {
		this.options = [
			{ label: 'Please Select One', value: null },
			{ label: 'Bill', value: 'bill' },
			{ label: 'GBD', value: 'gbd' },
			{ label: 'Survey Pictures', value: 'survey pictures' },
			{ label: 'Call Center Recording', value: 'call center recording' },
			{ label: 'Welcome call form', value: 'welcome call form' },
			{ label: 'Welcome call recoding', value: 'welcome call recoding' }

		]

		if (this.canEditProposalsAndAdministrateLeads) {
			this.options.push({ label: 'CPUC', value: 'cpuc' })
			this.options.push({ label: 'Change Order', value: 'change order' })
			this.options.push({ label: 'Design Plan', value: 'design plan' })
			this.options.push({ label: 'Proposal', value: 'proposal' })
			this.options.push({ label: 'SunRun Map', value: 'sunRun Map' })
			this.options.push({ label: 'Roof Work', value: 'roof work' })
			this.options.push({ label: 'Signed ACH', value: 'signed ACH' })
			this.options.push({ label: 'Signed NEM Document', value: 'signed NEM' })
			this.options.push({ label: 'Signed Solar Agreement', value: 'signed solar agreement' })
			this.options.push({ label: 'Final Design Plan', value: 'site plan' })

			this.options.push({ label: 'Quote', value: 'quote' })
			this.options.push({ label: 'Test Contract', value: 'test contract' })
		}

		if (this.canEditIntegrations) {
			this.options.push({ label: 'Savings Breakdown', value: 'savings breakdown' })
		}

		if (this.canCloseDeal) {
			this.options.push({ label: 'Signed NEM Document', value: 'signed NEM' })
			this.options.push({ label: 'Signed Solar Agreement', value: 'signed solar agreement' })
			this.options.push({ label: 'CPUC', value: 'signed CPUC' })
		}
	},
	methods: {
		updateSelection (data) {
			this.$emit('select', data)
		}
	}
}
</script>

<style scoped>

</style>
