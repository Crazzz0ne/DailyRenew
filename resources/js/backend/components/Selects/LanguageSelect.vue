<template>
  <div v-if="user">
    <label style="padding-top: 2%">Spoken Languages:</label>
    <MazSelect
      v-model="multipleValues"
      :options="optionsMultiple"
      multiple
      clearable
      size="sm"
      color="info"
      @input="saveChanges"
    >
      <i
        slot="icon-left"
        class="material-icons"
      >
        add_circle_outline
      </i>
    </MazSelect>
  </div>
</template>

<script>
import { MazSelect } from 'maz-ui'
import axios from 'axios'

export default {
	name: 'LanguageSelect',
	components: { MazSelect },
	props: {
		user: {
			type: Object,
			required: true,
			default: () => ({ language: 'N/A' })
		},
		placeholder: { type: String, required: false, default: 'Select Native Language' },
		optionsMultiple: {
			type: Array,
			required: false,
			default: () => ([
				{ label: 'English', value: 'english' },
				{ label: 'Spanish', value: 'spanish' }
			])
		}
	},
	data () {
		return {
			multipleValues: null
		}
	},
	computed: {},

	mounted () {
		this.multipleValues = this.user.languages
	},
	methods: {

		saveChanges () {
			console.log(this.multipleValues)
			axios.post(`/api/user/${this.user.id}/updateLanguages`,
				{
					languages: this.multipleValues
				}).then((response) => {
				console.log(response)
			}).catch(function (error) {
				console.log(error)
			})
		}
	}
}
</script>

<style scoped>

</style>
