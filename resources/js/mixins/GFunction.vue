<script>
export default {
	methods: {
		shittyObject (someObject) {
			if (!someObject) {
				return false
			}
			return Object.keys(someObject).length
		}
	}
}
</script>
