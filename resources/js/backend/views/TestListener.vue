<template>
  <div>
    <p>yeah this works so thats cool</p>
  </div>
</template>

<script>
export default {
	name: 'TestListener',
	created () {
		Echo.channel('test')
			.listen('Backend.LeadEvent', (e) => {
				console.log(e)
				console.log('something')
			})
	}
}
</script>

<style scoped>

</style>
