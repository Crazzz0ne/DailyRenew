<script>
export default {
	data: function () {
		return {
			avatarSize: 100
		}
	},
	computed: {
		scaleAvatarToDisplay () {
			if (this.isExtraSmall) { return 50 }
			if (this.isSmall) { return 60 }
			if (this.isMedium) { return 70 }
			if (this.isLarge) { return 80 }
			return 100
		}
	}
}

</script>
