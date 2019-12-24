<template>
	<div class="headcounts row col-12">
		<div class="col-4 col-md-3 mt-3">
			<div class="hexatable create" @click="createHeadcount">
				<div class="hexatable-name">
					<i class="fas fa-plus"></i>
				</div>
			</div>
		</div>

		<headcount v-for="(head, key) in headcounts"
			@selected="selectHead(head)"
			:key="head.id"
			:head="head"
			:index="key">
		</headcount>

		<slide-y-down-transition :duration="200">
			<div class="bottom-menu" v-show="selectedHeadcount.length > 0">
				<button class="btn btn-primary btn-block" 
						:disabled="selectedHeadcount.length > 1" 
						@click="addItems">Add item</button>
				<button class="btn btn-success btn-block" @click="billHeads">Bill headcounts</button>
			</div>
		</slide-y-down-transition>

		<slide-y-down-transition>
			<items :head="selectedHeadcount[0]" v-if="isAddingItem" @close="closeAddItem">

			</items>
		</slide-y-down-transition>

		<slide-y-down-transition>
			<bill :heads="selectedHeadcount" v-if="isBilling" @close="closeBill">

			</bill>
		</slide-y-down-transition>
	</div>
</template>
<script>
	import headcount from "./head.vue";
	import items from "./items.vue";
	import bill from "./bill.vue";

	export default {
		components: {
			headcount,
			items,
			bill,
		},

		props: [],

		data() { 
			return {
				headcounts: [],
				form: new Form({
					heads: [],
				}),
				selectedHeadcount: [],
				isAddingItem: false,
				isBilling: false,
			};
		},

		mounted() {
			this.getHeadcounts();
		},

		methods: {
			getHeadcounts() {
				window.events.$emit('unselect');
				this.selectedHeadcount = [];
				axios.get("/heads/active")
					.then(response => this.setHeadcounts(response))
					.catch(error => this.catchAjaxError(error));
			},

			setHeadcounts(response) {
				this.headcounts = response.data;
			},

			createHeadcount() {
				this.$swal({
					title: 'Activate headcount',
					input: 'text',
					inputPlaceholder: "Separate each headcount with ; ",
					showCancelButton: true,
				}).then((result) => {
					if(result) this.postHeadcount(result.value);
				})
			},

			postHeadcount(value) {
				this.form.heads = value.split(";");
				this.form.post("/heads/activate")
						.then(response => this.onSuccess(response))
						.catch(error => this.catchAjaxError(error));
			},

			onSuccess(response) {
				this.getHeadcounts();
			},

			selectHead(head) {
				let index = _.findIndex(this.selectedHeadcount, head);

				if(index < 0)
					this.selectedHeadcount.push(head);
				else
					this.selectedHeadcount.splice(index, 1)
			},

			addItems() {
				this.isAddingItem = true;
			},

			closeAddItem() {
				this.getHeadcounts();
				this.isAddingItem = false;
			},

			billHeads() {
				this.isBilling = true;
			},

			closeBill() {
				this.getHeadcounts();
				this.isBilling = false;
			}

		}
	}
</script>