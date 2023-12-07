<template>
  <div>
    <MazBtn
      class="maz-mr-2 maz-mb-2"
      @click="showModel = true"
    >
      Details
    </MazBtn>
    <MazDialog
      v-model="showModel"
    >
      <div slot="title">
        Days
      </div>

      <div>
        <MazSelect
          v-model="selectedUser"
          placeholder="Sales Rep"
          :options="repList"
        />

        <div class="lists-container">
          <div class="list-1">
            <h5 class="maz-mb-2">
              How many days by {{ compType }}
            </h5>
            <p class="maz-text-muted maz-mb-3" />
            <MazList>
              <MazListItem
                v-for="(time, index) in displayLeads"

                :key="index"
              >
                <p>
                  Lead :{{ time.leadId }}
                  <MazBtn
                    :size="'mini'"
                    fab
                    @click="openLead(time.leadId)"
                  >
                    <i class="fa fa-eye" />
                  </MazBtn>
                </p>
                <p class="maz-text-muted">
                  Days {{ time.days }}
                </p>
              </MazListItem>
            </MazList>
          </div>
        </div>
      </div>
    </MazDialog>
  </div>
</template>

<script>
export default {
	name: 'TimeToEventModel',
	props: [
		'details',
		'type'
	],
	data () {
		return {
			showModel: false,
			selectedUser: null

		}
	},
	computed: {
		compType () {
			switch (this.type) {
			case 'close':
				return 'start to close'
			case 'install':
				return ' close to install'
			case 'all':
				return 'start to install'
			}
		},
		displayLeads () {
			const user = [this.selectedUser]
			const filtered = Object.keys(this.details)
				.filter(key => user.includes(key))
				.reduce((obj, key) => {
					obj[key] = this.details[key]
					return obj
				}, {})

			const list = []

			$.each(filtered, function (v, k) {
				console.log('k', k)
				// filtered.time.forEach((v, k) => {
				$.each(k.time, function (skey, value) {
					console.log('asdasd')
					list.push({ leadId: skey, days: value })
				})
			})
			// $.each(filtered.time, function (skey, value) {
			//   console.log(skey, value)
			// })

			return list
		},
		repList () {
			if (Object.entries(this.details).length === 0) {
				return []
			}
			const users = []
			$.each(this.details, function (skey, value) {
				const u = {
					value: skey, label: value.name
				}
				users.push(u)
			})
			return users
		}
	},
	methods: {
		openLead: function (id) {
			const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
			window.open(routeData.href, '_blank')
		}
	}
}
</script>

<style scoped>

</style>
