<template>
  <MazCollapse class="maz-mb-5">
    <div slot="header-text">
      What Next?
    </div>
    <div class="maz-p-4">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col">
              {{ neededItems.message }}
            </div>
            <div class="col">
              <ul>
                <li v-for="item in neededItems.neededList">
                  {{ item }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MazCollapse>
</template>

<script>

export default {
	name: 'LeadNeededNext',
	props: {
		lead: Object,
		market: String,
		canSp2: Boolean,
		isBuilder: Boolean

	},

	data () {
		return {
			value: false
		}
	},
	computed: {
		neededItems () {
			const data = {}
			if (this.lead.epc === 'Sunrun') {
				switch (this.lead.status) {
				case 'new lead':
					if (this.market !== 'sub dealer') {
						if (this.lead.integrations_approved === null) {
							data.message = 'Request Integration'
						}
					}
					break
				case 'pending credit approval':
					data.message = 'Update the customers credit status'
					break
				case 'pending paper work':
					if (this.canSp2) {
						data.message = 'Book your SP2 or submit the following with the button bellow'
						const neededList = ['Solar Agreement', 'CPUC']
						data.neededList = neededList
					}
					break
				case 'close':
					data.message = 'Update the Site Survey date from Sunrun in Scout.'
					break
				case 'site survey':
					data.message = 'Let us know how it went and update Scout when we have an install date.'
					break
				case 'pending install':
					data.message = 'Install is coming up soon. Good work!'
					break
				case 'installed':
					data.message = 'We are just waiting to hear back on PTO'
					break
				case 'PTO':
					data.message = 'Pat yourself on the back you did it!'
					break
				}
			} else {
				switch (this.lead.status) {
				case 'new proposal':
					data.message = 'We need some information from the customer to start the proposal'
					const neededList = ['A Phone number', 'Email', 'Name on Bill', 'Power Company', 'Average Bill', 'KWH Yearly Usage']
					data.neededList = neededList
					break
				case 'submitted to proposal team':
					if (this.isBuilder) {
						data.message = 'Time to build'
						const neededList = ['proposal ID', 'System Size', 'Solar Rate', 'Monthly Payment', 'Offset', 'System']
					} else {
						data.message = 'Kick back and wait, We will get back to you soon.'
					}

					break
				case 'pending paper work':
					if (this.canSp2) {
						data.message = 'Book your SP2 or submit the following with the button bellow'
						const neededList = ['Solar Agreement', 'CPUC']
						data.neededList = neededList
					}
					break
				case 'close':
					data.message = 'Update the Site Survey date from Sunrun in Scout.'
					break
				case 'site survey':
					data.message = 'Let us know how it went and update Scout when we have an install date.'
					break
				case 'pending install':
					data.message = 'Install is coming up soon. Good work!'
					break
				case 'installed':
					data.message = 'We are just waiting to hear back on PTO'
					break
				case 'PTO':
					data.message = 'Pat yourself on the back you did it!'
					break
				}
			}
			return data
		}
	},
	created () {

	},
	mounted () {

	},
	methods: {}
}
</script>

<style scoped>
#notes {
    margin-top: 20px;
}
</style>
