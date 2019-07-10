<template>
	<div>
		<div class="container" v-if="!is_ordering">
			<div class="row">
				<hexatable :table="hexatable" 
					v-for="hexatable in tables" 
					:key="hexatable.id"
					@order="selectTable(hexatable)"
					@back="back">
				</hexatable>
			</div>
		</div>
		<div class="container-fluid" v-else>
			<hexaorder :paymethods="paymethods" :table="selectedTable" @back="back"></hexaorder>
		</div>
	</div>
</template>

<script>
	import hexatable from "./Table.vue";
	import hexaorder from "./Order.vue";
	export default {
		props: ['paymethods'],

		components: {
			hexatable,
			hexaorder,
		},

		data() {
			return {
				tables: [],
				selectedTable: '',
				is_ordering: false,
			};
		},

		mounted() {
			this.getTables();
		},

		methods: {
			back() {
				this.is_ordering = false;
			},

			getTables(error, tries = 0) {
				if(tries < 3)
					axios.get("/data/tables")
						.then(response => this.setTables(response))
						.catch(error => this.getTables(error, ++tries));
			},

			setTables(response) {
				this.tables = response.data;
			},

			selectTable(table) {
				this.selectedTable = table;
				this.is_ordering = true;
			},


		}
	}
</script>