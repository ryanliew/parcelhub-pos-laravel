<template>
	<div class="col-4 col-md-3 mt-3">
		<div class="hexatable"  
			:class="tableClass" 
			@click="toggleTable">

			<div class="hexatable-name">
				{{ table.name }}
			</div>

		</div>
	</div>
</template>

<script>
	export default {
		props: ['table'],
		data() {
			return {
				form: new Form({
					table_id: this.table.id,
				}),
				actual_table: this.table,
			};
		},

		methods: {
			toggleTable(error = "", tries = 0) {
				if(!this.actual_table.is_active) {
					if(tries < 3)
						this.form.post("/tables/activate")
							.then(response => this.setTable(response))
							.catch(error => this.toggleTable(error, ++tries));
				}
				else {
					this.openOrderDialog();
				}
			},

			setTable(response) {
				this.actual_table = response.table;
				this.openOrderDialog();
			},

			openOrderDialog() {
				console.log("emit");
				this.$emit("order");
			}
		},

		computed: {
			tableClass() {
				return this.actual_table.is_active ? "hexatable-active" : ""
			}
		}	
	}
</script>