<template>
	<div class="order-form row">
		<div class="col-md-7 border-right d-flex flex-column">
			<div>
				<b>Current time</b>: {{ currentTime }}
			</div>
			<div>
				<b>Table:</b> {{ table.name }}
			</div>
			<div class="order-items">
				<table class="table past-order" v-for="invoice in invoices" :key="invoice.id">
					<hexa-item :item="item" v-for="item in invoice.items" :key="item.id"></hexa-item>
				</table>
				<table class="table">
					<hexa-item :item="item" v-for="(item,index) in items" :key="index"></hexa-item>
				</table>
			</div>
			<div class="order-information">

			</div>
			<div class="controls d-flex">
				<button class="btn btn-small btn-secondary mr-2">
					Back
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="addHeadcount">
					Add headcount
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="checkoutHeadcount">
					Checkout headcount
				</button>
			</div>
			<headcount-selector 
				:table="table"
				:show="is_select_headcount"
				@close="is_select_headcount = false"
				@confirm="selectedHead"
				:currentFilter="headcount_type">

			</headcount-selector>
		</div>
		<div class="col-md-5 border">
			<item-selector
				@selected="selectItem"
			>

			</item-selector>
		</div>
	</div>
</template>

<script>
	import moment from 'moment';
	import HexaItem from "./HexaItem.vue";
	import ItemSelector from "./ItemSelector.vue";
	import HeadcountSelector from "./HeadcountSelector.vue";

	export default {
		props: ['table'],

		components: {
			HexaItem,
			ItemSelector,
			HeadcountSelector
		},

		data() {
			return {
				currentTime: '',
				invoices: [],
				items: [],
				headcount_type: '',
				is_select_headcount: false,
				is_adding_headcount: false,
				is_checking_out_headcount: false,
				headForm: new Form({
					heads: []
				}),
			};
		},

		mounted() {
			this.currentTime = moment().format('LL LTS');
			setInterval(() => this.updateCurrentTime(), 1000);
			this.getItems();
		},

		methods: {
			updateCurrentTime() {
				this.currentTime = moment().format('LL LTS');
			},

			getItems(error = "", tries = 0) {
				if(tries < 3)
					axios.get("/tables/" + this.table.id + "/items")
						.then(response => this.setItems(response))
						.catch(error => this.getItems(error, ++tries));
			},

			setItems(response) {
				this.invoices = response.data;
			},

			calculateItemTax(item) {
				let tax = 0;

				if(item.is_tax_inclusive) {
					tax = item.price - (Math.round(item.price / (item.tax.percentage / 100 + 1) * 100) / 100 );
				}
				else {
					tax = Math.round(item.price * item.tax.percentage) / 100;
				}

				return tax * item.unit;
			},

			calculateItemTotalPrice(item) {
				let total = item.price * item.unit;
				
				if(!item.is_tax_inclusive)
					total += this.calculateItemTax(item);

				return total;
			},

			selectItem(e) {
				let existing = _.findIndex(this.items, function(item){ return item.id == e.item.id; }.bind(e));

				let item = e.item;

				if(existing > -1) {
					this.items[existing].unit++;
					this.items[existing].tax_value = this.calculateItemTax(this.items[existing]);
					this.items[existing].total = this.calculateItemTotalPrice(this.items[existing]);
				} else {
					Vue.set(item, 'unit', 1);
					Vue.set(item, 'tax_value', this.calculateItemTax(item));
					Vue.set(item, 'total', this.calculateItemTotalPrice(item));
					this.items.push(item);
				}

			},

			addHeadcount() {
				this.headcount_type = 'inactive';
				this.is_select_headcount = true;
				this.is_adding_headcount = true;
			},

			checkoutHeadcount() {
				this.headcount_type = 'active';
				this.is_select_headcount = true;
				this.is_checking_out_headcount = true;
			},

			selectedHead(e) {
				this.is_select_headcount = false;

				this.headForm.heads = [];

				e.heads.forEach(function(head){
					this.headForm.heads.push(head.id);
				}.bind(this));

				if(this.is_adding_headcount) {

					this.headForm.post("/heads/activate")
						.then(response => this.onSuccess(response))
						.catch(error => this.onError(error));

				} else if (this.is_checking_out_headcount) {

					this.headForm.post("/heads/deactivate")
						.then(response => this.onDeactivateSuccess(response))
						.catch(error => this.onError(error));

				}

				this.is_adding_headcount = false;
				this.is_checking_out_headcount = false;
			},

			onSuccess(response) {

			},

			onError(error) {

			},

			onDeactivateSuccess(response) {

			},
		}	
	}
</script>