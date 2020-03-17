<template>
	<div>
		
	</div>
</template>

<script>

	export default {
		props: ['message', 'confirming', 'secondary', 'hideconfirm'],
		data() {
			return {

			};
		},

		watch: {
			confirming(newVal, oldVal) {
				if(newVal) {
					this.$swal({
						title: this.message,
						type: 'warning',
						html: this.secondary,
						showCancelButton: true,
						showConfirmButton: this.hideconfirm ? false : true,
					}).then((result) => {
						if(result.value) {
							this.$emit('confirm');
						}
						else {
							this.$emit('cancel');
						}
					})
				}
			}
		}	
	}
</script>