<template>
  <div v-if="user">
    <label style="padding-top: 2%">Timezone: </label>
    <MazSelect
      v-model="selectedTimezone"
      :options="options"
      search
      search-placeholder="Search Timezones"
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
	name: 'TimezoneSelect',
	components: { MazSelect },
	props: {
		user: {
			type: Object,
			required: true,
			default: () => ({ timezone: 'UTC' })
		},
		options: {
			type: Array,
			required: false,
			default () {
				return [
					{ value: 'America/Puerto_Rico', label: 'Puerto Rico (Atlantic)' },
					{ value: 'America/New_York', label: 'New York (Eastern)' },
					{ value: 'America/Chicago', label: 'Chicago (Central)' },
					{ value: 'America/Denver', label: 'Denver (Mountain)' },
					{ value: 'America/Phoenix', label: 'Phoenix (MST)' },
					{ value: 'America/Los_Angeles', label: 'Los Angeles (Pacific)' },
					{ value: 'America/Anchorage', label: 'Anchorage (Alaska)' },
					{ value: 'Pacific/Honolulu', label: 'Honolulu (Hawaii)' },
					{ value: 'America/Adak', label: 'Adak' },
					{ value: 'America/Anchorage', label: 'Anchorage' },
					{ value: 'America/Anguilla', label: 'Anguilla' },
					{ value: 'America/Antigua', label: 'Antigua' },
					{ value: 'America/Araguaina', label: 'Araguaina' },
					{ value: 'America/Argentina/Buenos_Aires', label: 'Argentina - Buenos Aires' },
					{ value: 'America/Argentina/Catamarca', label: 'Argentina - Catamarca' },
					{ value: 'America/Argentina/ComodRivadavia', label: 'Argentina - ComodRivadavia' },
					{ value: 'America/Argentina/Cordoba', label: 'Argentina - Cordoba' },
					{ value: 'America/Argentina/Jujuy', label: 'Argentina - Jujuy' },
					{ value: 'America/Argentina/La_Rioja', label: 'Argentina - La Rioja' },
					{ value: 'America/Argentina/Mendoza', label: 'Argentina - Mendoza' },
					{ value: 'America/Argentina/Rio_Gallegos', label: 'Argentina - Rio Gallegos' },
					{ value: 'America/Argentina/Salta', label: 'Argentina - Salta' },
					{ value: 'America/Argentina/San_Juan', label: 'Argentina - San Juan' },
					{ value: 'America/Argentina/San_Luis', label: 'Argentina - San Luis' },
					{ value: 'America/Argentina/Tucuman', label: 'Argentina - Tucuman' },
					{ value: 'America/Argentina/Ushuaia', label: 'Argentina - Ushuaia' },
					{ value: 'America/Aruba', label: 'Aruba' },
					{ value: 'America/Asuncion', label: 'Asuncion' },
					{ value: 'America/Atikokan', label: 'Atikokan' },
					{ value: 'America/Atka', label: 'Atka' },
					{ value: 'America/Bahia', label: 'Bahia' },
					{ value: 'America/Barbados', label: 'Barbados' },
					{ value: 'America/Belem', label: 'Belem' },
					{ value: 'America/Belize', label: 'Belize' },
					{ value: 'America/Blanc-Sablon', label: 'Blanc-Sablon' },
					{ value: 'America/Boa_Vista', label: 'Boa Vista' },
					{ value: 'America/Bogota', label: 'Bogota' },
					{ value: 'America/Boise', label: 'Boise' },
					{ value: 'America/Buenos_Aires', label: 'Buenos Aires' },
					{ value: 'America/Cambridge_Bay', label: 'Cambridge Bay' },
					{ value: 'America/Campo_Grande', label: 'Campo Grande' },
					{ value: 'America/Cancun', label: 'Cancun' },
					{ value: 'America/Caracas', label: 'Caracas' },
					{ value: 'America/Catamarca', label: 'Catamarca' },
					{ value: 'America/Cayenne', label: 'Cayenne' },
					{ value: 'America/Cayman', label: 'Cayman' },
					{ value: 'America/Chicago', label: 'Chicago' },
					{ value: 'America/Chihuahua', label: 'Chihuahua' },
					{ value: 'America/Coral_Harbour', label: 'Coral Harbour' },
					{ value: 'America/Cordoba', label: 'Cordoba' },
					{ value: 'America/Costa_Rica', label: 'Costa Rica' },
					{ value: 'America/Cuiaba', label: 'Cuiaba' },
					{ value: 'America/Curacao', label: 'Curacao' },
					{ value: 'America/Danmarkshavn', label: 'Danmarkshavn' },
					{ value: 'America/Dawson', label: 'Dawson' },
					{ value: 'America/Dawson_Creek', label: 'Dawson Creek' },
					{ value: 'America/Denver', label: 'Denver' },
					{ value: 'America/Detroit', label: 'Detroit' },
					{ value: 'America/Dominica', label: 'Dominica' },
					{ value: 'America/Edmonton', label: 'Edmonton' },
					{ value: 'America/Eirunepe', label: 'Eirunepe' },
					{ value: 'America/El_Salvador', label: 'El Salvador' },
					{ value: 'America/Ensenada', label: 'Ensenada' },
					{ value: 'America/Fortaleza', label: 'Fortaleza' },
					{ value: 'America/Fort_Wayne', label: 'Fort Wayne' },
					{ value: 'America/Glace_Bay', label: 'Glace Bay' },
					{ value: 'America/Godthab', label: 'Godthab' },
					{ value: 'America/Goose_Bay', label: 'Goose Bay' },
					{ value: 'America/Grand_Turk', label: 'Grand Turk' },
					{ value: 'America/Grenada', label: 'Grenada' },
					{ value: 'America/Guadeloupe', label: 'Guadeloupe' },
					{ value: 'America/Guatemala', label: 'Guatemala' },
					{ value: 'America/Guayaquil', label: 'Guayaquil' },
					{ value: 'America/Guyana', label: 'Guyana' },
					{ value: 'America/Halifax', label: 'Halifax' },
					{ value: 'America/Havana', label: 'Havana' },
					{ value: 'America/Hermosillo', label: 'Hermosillo' },
					{ value: 'America/Indiana/Indianapolis', label: 'Indiana - Indianapolis' },
					{ value: 'America/Indiana/Knox', label: 'Indiana - Knox' },
					{ value: 'America/Indiana/Marengo', label: 'Indiana - Marengo' },
					{ value: 'America/Indiana/Petersburg', label: 'Indiana - Petersburg' },
					{ value: 'America/Indiana/Tell_City', label: 'Indiana - Tell City' },
					{ value: 'America/Indiana/Vevay', label: 'Indiana - Vevay' },
					{ value: 'America/Indiana/Vincennes', label: 'Indiana - Vincennes' },
					{ value: 'America/Indiana/Winamac', label: 'Indiana - Winamac' },
					{ value: 'America/Indianapolis', label: 'Indianapolis' },
					{ value: 'America/Inuvik', label: 'Inuvik' },
					{ value: 'America/Iqaluit', label: 'Iqaluit' },
					{ value: 'America/Jamaica', label: 'Jamaica' },
					{ value: 'America/Jujuy', label: 'Jujuy' },
					{ value: 'America/Juneau', label: 'Juneau' },
					{ value: 'America/Kentucky/Louisville', label: 'Kentucky - Louisville' },
					{ value: 'America/Kentucky/Monticello', label: 'Kentucky - Monticello' },
					{ value: 'America/Knox_IN', label: 'Knox IN' },
					{ value: 'America/La_Paz', label: 'La Paz' },
					{ value: 'America/Lima', label: 'Lima' },
					{ value: 'America/Los_Angeles', label: 'Los Angeles' },
					{ value: 'America/Louisville', label: 'Louisville' },
					{ value: 'America/Maceio', label: 'Maceio' },
					{ value: 'America/Managua', label: 'Managua' },
					{ value: 'America/Manaus', label: 'Manaus' },
					{ value: 'America/Marigot', label: 'Marigot' },
					{ value: 'America/Martinique', label: 'Martinique' },
					{ value: 'America/Matamoros', label: 'Matamoros' },
					{ value: 'America/Mazatlan', label: 'Mazatlan' },
					{ value: 'America/Mendoza', label: 'Mendoza' },
					{ value: 'America/Menominee', label: 'Menominee' },
					{ value: 'America/Merida', label: 'Merida' },
					{ value: 'America/Mexico_City', label: 'Mexico City' },
					{ value: 'America/Miquelon', label: 'Miquelon' },
					{ value: 'America/Moncton', label: 'Moncton' },
					{ value: 'America/Monterrey', label: 'Monterrey' },
					{ value: 'America/Montevideo', label: 'Montevideo' },
					{ value: 'America/Montreal', label: 'Montreal' },
					{ value: 'America/Montserrat', label: 'Montserrat' },
					{ value: 'America/Nassau', label: 'Nassau' },
					{ value: 'America/New_York', label: 'New York' },
					{ value: 'America/Nipigon', label: 'Nipigon' },
					{ value: 'America/Nome', label: 'Nome' },
					{ value: 'America/Noronha', label: 'Noronha' },
					{ value: 'America/North_Dakota/Center', label: 'North Dakota - Center' },
					{ value: 'America/North_Dakota/New_Salem', label: 'North Dakota - New Salem' },
					{ value: 'America/Ojinaga', label: 'Ojinaga' },
					{ value: 'America/Panama', label: 'Panama' },
					{ value: 'America/Pangnirtung', label: 'Pangnirtung' },
					{ value: 'America/Paramaribo', label: 'Paramaribo' },
					{ value: 'America/Phoenix', label: 'Phoenix' },
					{ value: 'America/Port-au-Prince', label: 'Port-au-Prince' },
					{ value: 'America/Porto_Acre', label: 'Porto Acre' },
					{ value: 'America/Port_of_Spain', label: 'Port of Spain' },
					{ value: 'America/Porto_Velho', label: 'Porto Velho' },
					{ value: 'America/Puerto_Rico', label: 'Puerto Rico' },
					{ value: 'America/Rainy_River', label: 'Rainy River' },
					{ value: 'America/Rankin_Inlet', label: 'Rankin Inlet' },
					{ value: 'America/Recife', label: 'Recife' },
					{ value: 'America/Regina', label: 'Regina' },
					{ value: 'America/Resolute', label: 'Resolute' },
					{ value: 'America/Rio_Branco', label: 'Rio Branco' },
					{ value: 'America/Rosario', label: 'Rosario' },
					{ value: 'America/Santa_Isabel', label: 'Santa Isabel' },
					{ value: 'America/Santarem', label: 'Santarem' },
					{ value: 'America/Santiago', label: 'Santiago' },
					{ value: 'America/Santo_Domingo', label: 'Santo Domingo' },
					{ value: 'America/Sao_Paulo', label: 'Sao Paulo' },
					{ value: 'America/Scoresbysund', label: 'Scoresbysund' },
					{ value: 'America/Shiprock', label: 'Shiprock' },
					{ value: 'America/St_Barthelemy', label: 'St Barthelemy' },
					{ value: 'America/St_Johns', label: 'St Johns' },
					{ value: 'America/St_Kitts', label: 'St Kitts' },
					{ value: 'America/St_Lucia', label: 'St Lucia' },
					{ value: 'America/St_Thomas', label: 'St Thomas' },
					{ value: 'America/St_Vincent', label: 'St Vincent' },
					{ value: 'America/Swift_Current', label: 'Swift Current' },
					{ value: 'America/Tegucigalpa', label: 'Tegucigalpa' },
					{ value: 'America/Thule', label: 'Thule' },
					{ value: 'America/Thunder_Bay', label: 'Thunder Bay' },
					{ value: 'America/Tijuana', label: 'Tijuana' },
					{ value: 'America/Toronto', label: 'Toronto' },
					{ value: 'America/Tortola', label: 'Tortola' },
					{ value: 'America/Vancouver', label: 'Vancouver' },
					{ value: 'America/Virgin', label: 'Virgin' },
					{ value: 'America/Whitehorse', label: 'Whitehorse' },
					{ value: 'America/Winnipeg', label: 'Winnipeg' },
					{ value: 'America/Yakutat', label: 'Yakutat' },
					{ value: 'America/Yellowknife', label: 'Yellowknife' }
					// The rest can be found here. https://gist.github.com/ykessler/3349960
				]
			}
		}
	},
	data () {
		return {
			placeholder: 'Select Timezone',
			selectedTimezone: null
		}
	},
	mounted () {
		this.selectedTimezone = this.user.timezone
	},
	methods: {

		saveChanges () {
			axios.post(`/api/user/${this.user.id}/timezone`, { timezone: this.selectedTimezone })
				.then((response) => {
				}).catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
