<template>
  <div class="container">
    <Model
      v-if="showModal"
      @close="showModal = false"
    >
      <h3 slot="header">
        Rotate Phone
      </h3>
    </Model>
    <div class="row">
      <div class="col">
        <div class="card p-3 fancy-body-card">
          <div class="row pb-3">
            <div class="col-md-6 col-sm-12">
              <div>
                <label for="selectedStanding">Selected Standing</label>
              </div>
              <select
                id="selectedStanding"
                v-model="selectedStanding"
                name="selectedStanding"
                class="text-capitalize"
              >
                <option value="">
                  Make A Selection
                </option>
                <option
                  v-for="heads in headers"
                  :value="heads"
                >
                  {{ heads }}
                </option>
              </select>
            </div>
          </div>
          <template v-for="(standing, index1) in standings">
            <!--                        <span>{{ standings }}</span>-->
            <StandingTable
              :table-data="standing"
              :name="index1"
              :selected-standing="selectedStanding"
              class="py-2"
            />
          </template>
          <div class="float-left pt-5">
            <a
              href="/dashboard/officestanding"
              data-toggle="tooltip"
              data-placement="top"
              title="Back"
              class="btn btn-info"
              data-original-title="View"
            >
              Back</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// todo: make 1 gold 2 silver 3 bronze. with a low opacity. it is tied to the year rank and follows top index gets bold on sort
import Model from '../includes/Model'
export default {
	name: 'Standings',
	components: { Model },
	props: {
		standings: Object,
		showModel: Boolean

	},
	data () {
		return {
			selectedStanding: '',
			showModal: false,
			window: {
				width: 0,
				height: 0
			}

		}
	},
	computed: {
		headers: function () {
			const heads = []
			Object.keys(this.standings).forEach(key => {
				heads.push(key)
			})
			return heads
		}

	},
	created () {
		window.addEventListener('resize', this.handleResize)
		this.handleResize()
	},
	destroyed () {
		window.removeEventListener('resize', this.handleResize)
	},
	methods: {
		handleResize () {
			if (window.innerWidth < 790) {
				this.showModal = true
				console.log('errr')
			} else {
				this.showModal = false
			}
		}

	}
}
</script>

<style scoped>

</style>
