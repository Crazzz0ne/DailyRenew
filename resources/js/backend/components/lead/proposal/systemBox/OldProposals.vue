<template>
  <MazDialog
    v-model="showModel"
  >
    <div slot="title">
      Basic Dialog
    </div>
    Dialog Content basic
  </MazDialog>
</template>

<script>
export default {
	name: 'OldProposals',
	props: ['model'],
	data () {
		return {
			showModel: false
		}
	},
	watch: {
		model () {
			this.showModel = true
		}
	}

}
</script>

<style scoped>

</style>
