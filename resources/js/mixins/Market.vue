<script>
export default {
	methods: {
		$market (marketName) {
			if (marketName === LeadMarket) {
				return true
			} else {
				return false
			}
		}
	}
}
</script>
