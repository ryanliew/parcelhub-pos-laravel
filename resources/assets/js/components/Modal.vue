<template>
	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modelLabel" :id="id" aria-hidden="true">
		<div class="modal-dialog" :class="size" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><slot name="header"></slot></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          	<span aria-hidden="true">&times;</span>
			        </button>
				</div>
				<div class="modal-body">

		    		<slot></slot>
		      	</div>
		      	<div class="modal-footer">
		      		<slot name="footer"></slot>
		      	</div>
			</div>
	  	</div>
	</div>
</template>

<script>
	export default {
		props: ['active', 'id', 'size'],
		data() {
			return {

			};
		},

		mounted() {
			$("#" + this.id).on("show.bs.modal", function(e){
				this.open();
			}.bind(this));
			$("#" + this.id).on("hide.bs.modal", function(e){
				this.close();
			}.bind(this));
		},	

		methods: {
			open() {
				this.$emit('open');
			},

			close() {
				this.$emit('close');
			}
		},

		watch: {
			active(newVal, oldval) {
				if(newVal)
					$("#" + this.id).modal();
				else 
					$("#" + this.id).modal("hide");
			}
		}	
	}
</script>