<template>
	<div class="order-form row">
		<div class="col-md-7 border-right d-flex flex-column">
			<div class="d-none">
				<b>Current time</b>: {{ currentTime }}
			</div>
			<div class="text-center table-name row align-items-center">
				<div class="col-6 col-md-12">
					<b>Table:</b> {{ table.name }}
				</div>
				<div class="col-6 display-mobile">
					<button class="btn btn-primary display-mobile" @click="is_selecting_items = true">Add Item</button>
				</div>
			</div>


			<div class="order-items">
				<table class="table">
					<hexa-item :item="item" v-for="(item,index) in orderForm.items" :key="item.sku" @delete="deleteItem(index)"></hexa-item>
					<template  v-for="invoice in invoices" v-if="!invoice.canceled_on">
						<hexa-item class="past-order" :item="item" v-for="item in invoice.items" :key="item.id" @delete="deleteInvoiceItem(item.id)" :canDelete="!session"></hexa-item>
					</template>
					
				</table>
			</div>

			<div class="order-information row my-3">
				<div class="col-6 col-md-4">
					<div class="d-flex flex-column">
						<selector-input :potentialData="discountTypes"
							v-model="selectedDiscountType" 
							:defaultData="selectedDiscountType"
							placeholder="Select discount type"
							:required="false"
							label="Discount type"
							name="discount_type"
							:editable="!session"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('discount_type')">
						</selector-input>

						<text-input v-model="form.discount_amount" 
							:defaultValue="form.discount_amount"
							:required="false"
							type="number"
							label="Discount"
							name="discount"
							:editable="!session"
							:focus="true"
							:hideLabel="false"
							:error="form.errors.get('discount')">
						</text-input>
					</div>
				</div>
				<div class="col-6 col-md-4">
					<div class="d-flex flex-column">
						<text-input v-model="form.paid" 
							:defaultValue="form.paid"
							:required="false"
							type="number"
							label="Paid"
							name="paid"
							:editable="!session"
							:focus="true"
							:hideLabel="false"
							:error="form.errors.get('paid')">
						</text-input>

						<selector-input :potentialData="payment_methods"
							v-model="selectedPaymentType" 
							:defaultData="selectedPaymentType"
							placeholder="Select payment method"
							:required="false"
							label="Payment method"
							name="payment_method"
							:editable="!session"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('payment_method')">
						</selector-input>
					</div>
				</div>
				<div class="col-md-4">
					<div class="order-summary d-flex">
						<div class="col-6 col-md-12">
							<div>
								<b>Subtotal:</b> RM{{ subtotal.toFixed(2) }}
							</div>
							<!-- <div>
								<b>Tax:</b> RM{{ tax.toFixed(2) }}
							</div> -->
							<div>
								<b>Discount:</b> RM{{ discountValue.toFixed(2) }}
							</div>
						</div>
						<div class="col-6 col-md-12">
							<div>
								<b>Rounding:</b> RM{{ rounding.toFixed(2) }}
							</div>
							<div>
								<b>Total:</b> RM{{ rounded_total.toFixed(2) }}
							</div>
							<div>
								<b>Change:</b> RM{{ change.toFixed(2) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="controls d-flex mb-3">
				<button class="btn btn-small btn-secondary mr-2" @click="back">
					Back
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="addHeadcount" v-if="!session">
					Add headcount
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="checkoutHeadcount" v-if="!session">
					Checkout headcount
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="placeOrder" :disabled="!canPlaceOrder" v-if="!session">
					Place order
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="guestCheck">
					Guest check
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="closeTable" :disabled="!canCloseTable"  v-if="!session">
					Close table
				</button>
				<button class="btn btn-small btn-primary mr-2" @click="printReceipt" v-if="session">
					Print receipt
				</button>
			</div>
			<headcount-selector 
				:table="table"
				:show="is_select_headcount"
				@close="is_select_headcount = false"
				@confirm="selectedHead"
				:currentFilter="headcount_type">

			</headcount-selector>

			<modal :active="is_selecting_items"
				id="items-selector"
				@close="is_selecting_items = false">
				<item-selector
					@selected="selectItem"
					key="2"
				>

				<template slot="header">
					Select item
				</template>

				</item-selector>
			</modal>
		</div>

		<div class="col-md-5 border display-desktop">
			<item-selector
				@selected="selectItem"
				key="1"
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
		props: ['table', 'paymethods', 'session'],

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
				payment_methods: [],

				headcount_type: '',
				is_select_headcount: false,
				is_adding_headcount: false,
				is_checking_out_headcount: false,

				is_selecting_items: false,

				headForm: new Form({
					heads: []
				}),

				orderForm: new Form({
					items: []
				}),

				tableForm: new Form({
					table_id: this.table.id
				}),

				form: new Form({
					total: 0,
					subtotal: 0,
					tax: 0,
					discount_type: "",
					discount_amount: 0,
					discount_value: "",
					paid: "",
					payment_method: "",
					rounding: "",
				}),

				discountTypes: [
					{label: "%", value: "%"},
					{label: "RM", value: "RM"},
				],
				selectedDiscountType: "",
				selectedPaymentType: ""

			};
		},

		mounted() {
			if(this.session) {
				this.currentTime = this.session.deactivated_at;
				this.invoices = this.session.invoices;
				this.form.paid = this.session.paid;
				this.selectedPaymentType = {label: this.session.payment_type, value: this.session.payment_type};
				this.form.discount = this.session.discount;
				this.selectedDiscountType = this.session.discount_mode ? {label: this.session.discount_mode, value: this.session.discount_mode} : "";
			} else {
				this.currentTime = moment().format('LL LTS');
				setInterval(() => this.updateCurrentTime(), 1000);
				this.setPaymentMethods();
				this.getItems();
			}
		},

		methods: {
			back() {
				this.$emit('back');
			},
			
			setPaymentMethods() {
				this.payment_methods = this.paymethods.map(function(m){
					let obj = {};

					obj['label'] = m.name;
					obj['value'] = m.name;

					return obj;
				});

				this.selectedPaymentType = this.payment_methods[0];
			},

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

			deleteItem(index) {
				this.orderForm.items.splice(index, 1);
			},

			deleteInvoiceItem(id) {
				axios.post("/items/destroy/" + id)
					.then(response => this.deleteInvoiceItemSuccess(response))
					.catch(error => this.handleError(error));
			},

			handleError(error) {
				console.log(error);
			},

			deleteInvoiceItemSuccess(response) {
				this.setItems(response);
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
				let selectedItem = e.item ? e.item : e;

				let existing = _.findIndex(this.orderForm.items, function(item){ return selectedItem.description == item.description; }.bind(selectedItem));

				if(existing > -1) {
					this.orderForm.items[existing].unit++;
					this.orderForm.items[existing].tax_value = this.calculateItemTax(this.orderForm.items[existing]);
					this.orderForm.items[existing].total = this.calculateItemTotalPrice(this.orderForm.items[existing]);
				} else {
					Vue.set(selectedItem, 'unit', 1);
					Vue.set(selectedItem, 'tax_value', this.calculateItemTax(selectedItem));
					Vue.set(selectedItem, 'total', this.calculateItemTotalPrice(selectedItem));
					this.orderForm.items.push(selectedItem);
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
					this.headForm.heads.push({id: head.id, member: head.member});
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

			placeOrder(error = "", tries = 0) {
				if(tries == 0)
					this.orderForm.post("/tables/" + this.table.id + "/order")
						.then(response => this.setNewInvoice(response))
						.catch(error => this.placeOrder(error, ++tries));
			},

			setNewInvoice(response) {
				this.invoices.push(response.invoice);
				this.orderForm.items = [];
			},

			closeTable(error = "", tries = 0) {
				if(tries == 0) {
					if(this.invoices.length > 0) {
						
						this.setFormValues();
						 
						this.form.post("/tables/" + this.table.id + "/close")
							.then(response => this.tableClosed(response))
							.catch(error => this.closeTable(error, ++tries));
					} else {
						this.tableForm.post('/tables/activate')
							.then(response => this.tableDeactivated(response))
							.catch(error => this.closeTable(error, ++tries));
					}
				}


			},	

			setFormValues() {
				// Set the calculated data to form
				this.form.total = this.rounded_total;
				this.form.subtotal = this.subtotal;
				this.form.tax = this.tax;
				this.form.discount_type = this.selectedDiscountType;
				// this.form.discount_amount = this.form.discount_amount; // Commented as it is user input
				this.form.discount_value = this.discountValue; 
				// this.form.paid = this.form.paid; // Commented as it is user input
				// this.form.payment_method = this.form.payment_method;
				this.form.rounding = this.rounding;
			},

			tableDeactivated(response) {
				window.location.reload();
			},

			tableClosed(response) {
				window.open(response.redirect_url);
				window.location.reload();
			},

			guestCheck(error, tries = 0) {
				this.setFormValues();
				if(tries < 3)
					this.form.post("/tables/" + this.table.id + "/check", false)
						.then(response => this.print(response))
						.catch(error => this.guestCheck(error, ++tries));
			},

			print(response) {
				window.open(response.redirect_url);
			},

			confirmOrder() {

			},

			onSuccess(response) {

			},

			onError(error) {

			},

			onDeactivateSuccess(response) {

				let products = response.products;

				_.forEach(products, function(product){
					this.selectItem(product);
				}.bind(this));

				this.placeOrder();
			},

			printReceipt() {
				window.open("/sessions/" + this.session.id + "/receipt");
			}
		},

		computed: {
			invoicesSubtotal() {
				return _.sumBy(this.invoices, function(invoice){
					return _.sumBy(invoice.items, function(item){ return item.total_price; })
				});
			},

			invoicesTax() {
				return _.sumBy(this.invoices, function(invoice){
					return _.sumBy(invoice.items, function(item){ return item.tax; });
				});
			},

			subtotal() {
				return _.sumBy(this.orderForm.items, function(item){ return item.total; }) + this.invoicesSubtotal;
			},

			tax() {
				return _.sumBy(this.orderForm.items, function(item){ return item.tax_value; }) + this.invoicesTax;
			},

			total() {
				return this.subtotal - this.discountValue;
			},

			discountValue() {
				let discount = this.form.discount_amount;

				if(this.selectedDiscountType.value == "%")
					discount = this.subtotal * this.form.discount_amount / 100;

				return parseFloat(discount);
			},

			rounded_total() {
				return Math.round((this.total + this.rounding) * 100) / 100;
			},

			rounding() {
				let rounded_total = Math.round(this.total * 100 / 5 ) / 100 * 5;
				let value = this.total - rounded_total;

				if(value !== 0)
					return value * -1;

				return 0.00;
			},

			change() {
				return this.form.paid > 0 ? this.form.paid - this.rounded_total : 0 ;
			},

			canPlaceOrder() {
				return this.orderForm.items.length > 0;
			},

			canCloseTable() {
				return !this.canPlaceOrder
						&& this.form.paid >= this.rounded_total;
			},

			placeOrderMessage() {
				let message = "";

				if(this.orderForm.items.length == 0) {
					message = "No items to place order.";
				}

				return message;
			},

			canCloseOrder() {
				return this.form.paid >= this.total;
			},

			closeOrderMessage() {
				let message = "";

				if(this.form.paid < this.total)
					message = "Must pay full amount";

				return message;
			},


		},

		watch:{
			selectedDiscountType(newValue) {
				if(newValue) {
					this.form.discount_type = newValue.value;
				} else {
					this.form.discount_type = "";
				}
			},

			selectedPaymentType(newValue) {
				if(newValue) {
					this.form.payment_method = newValue.value;
				} else {
					this.form.payment_method = "";
				}
			}
		}	
	}
</script>