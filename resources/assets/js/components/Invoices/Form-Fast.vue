<template>
	<div>
		<form @submit.prevent="submit" 
			@keydown="form.errors.clear($event.target.name)" 
			@input="form.errors.clear($event.target.name)">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<!-- Start Invoice Section -->
						<div class="col-6">
							<p v-if="invoiceNumber" class="mb-0"><b>Invoice number</b>: {{ invoiceNumber }}</p>
							<p><b v-if="!invoiceNumber">Current time</b><b v-else>Created time</b>: {{ currentTime }}</p>
							<selector-input :potentialData="types"
								v-model="selectedType" 
								:defaultData="selectedType"
								placeholder="Select type"
								:required="true"
								label="Type"
								name="type"
								:editable="canEdit"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('type')">
							</selector-input>
							<selector-input :potentialData="customers"
								v-model="selectedCustomer" 
								:defaultData="selectedCustomer"
								placeholder="Select customer"
								:required="true"
								label="Customer"
								name="customer_id"
								:editable="canEdit"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('customer_id')"
								v-if="form.type == 'Customer'"
								addon="createCustomer"
								@createCustomer="createCustomer">
							</selector-input>
							<text-input v-model="form.remarks" 
								:defaultValue="form.remarks"
								:required="false"
								type="text"
								label="Remarks"
								name="remarks"
								:editable="canEdit"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('remarks')">
							</text-input>

							<div class="d-flex justify-content-start mb-3">
								<a v-if="selectedCustomer && invoice" target="_blank" :href="'/invoices/do/' + invoice" type="button" class="btn btn-sm btn-success mr-2">Print delivery note</a>
								<a v-else-if="invoice" target="_blank" :href="'/invoices/receipt/' + invoice" type="button" class="btn btn-sm btn-success mr-2">Print receipt</a>
								<a v-if="invoice" target="_blank" :href="'/invoices/preview/' + invoice" type="button" class="btn btn-sm btn-success mr-2">Print invoice</a>
								<button type="submit" class="btn btn-sm btn-primary" :disabled="!canSubmit || !canEdit" :title="editTooltip">Confirm (F7)</button>
							</div>
						</div>

						<div class="col-6">
							<b>Tax:</b> {{ tax | price }}
							<div class="row mt-2">
								<div class="col">
									<text-input v-model="form.discount" 
										:defaultValue="form.discount"
										:required="false"
										type="number"
										label="Discount"
										name="discount"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('discount')">
									</text-input>
								</div>
								<div class="col">
									<selector-input :potentialData="modes"
										v-model="selectedDiscountMode" 
										:defaultData="selectedDiscountMode"
										placeholder="Select discount mode"
										:required="false"
										label="Discount mode"
										name="discount_mode"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('discount_mode')">
									</selector-input>
								</div>
							</div>

							<b>Rounding:</b> RM{{ rounding | price }}<br>
							<b>Total:</b> RM{{ rounded_total | price }}

							<div class="row mt-2" v-if="form.type !== 'Customer'">
								<div class="col">
									<text-input v-model="form.paid" 
										:defaultValue="form.paid"
										:required="true"
										type="number"
										label="Paid"
										name="paid"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('paid')">
									</text-input>
								</div>
								<div class="col">
									<selector-input :potentialData="payment_types"
										v-model="selectedPaymentType" 
										:defaultData="selectedPaymentType"
										placeholder="Select payment type"
										:required="true"
										label="Payment type"
										name="payment_type"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('payment_type')">
									</selector-input>
								</div>
							</div>

							<div v-if="form.type !== 'Customer'"><b>Change:</b> RM{{ change | price }}</div>
						</div>	
					</div>
				</div>
			</div>

			<div class="card mt-3">
				<div class="card-body">
					<button type="button" class="btn btn-sm btn-primary mb-3" @click="addItem" :disabled="!canAddItem">Add Item (F8)</button>
					<div class="invoice-items">

						<div class="header">Nr:</div>
						<div class="header">Track code:</div>
						<div class="header">Product type:</div>
						<div class="header">Zone type:</div>
						<div class="header">Zone:</div>
						<div class="header">Weight(kg):</div>
						<div class="header">Dim wt(kg):</div>
						<div class="header">Courier:</div>
						<div class="header">SKU:</div>
						<div class="header">Description:</div>
						<div class="header">Price:</div>
						<div class="header">Unit:</div>
						<div class="header">Total price:</div>
						<div class="header"></div>
					</div>
					<template v-for="(item, index) in form.items">
						<item-row :index="index" :canEdit="canEditItem" :item="item" :product_types="product_types" :zone_types="zone_types" :couriers="couriers" :defaultProductType="default_product_type" :selectedType="selectedType" :selectedCustomer="selectedCustomer" @delete="deleteItem(index)" @update="updateItem($event, index)" @addItem="addItem"></item-row>
					</template>
				</div>
			</div>
			<div class="card mt-3">
				<!-- Start footer section -->
				<div class="card-body">

					<div class="d-flex justify-content-start mb-3">
						<a v-if="selectedCustomer && invoice" target="_blank" :href="'/invoices/do/' + invoice" type="button" class="btn btn-sm btn-success mr-2">Print delivery note</a>
						<a v-else-if="invoice" target="_blank" :href="'/invoices/receipt/' + invoice" type="button" class="btn btn-sm btn-success mr-2">Print receipt</a>
						<a v-if="invoice" target="_blank" :href="'/invoices/preview/' + invoice" type="button" class="btn btn-sm btn-success mr-2">Print invoice</a>
						<button type="submit" class="btn btn-sm btn-primary" :disabled="!canSubmit || !canEdit" :title="editTooltip">Confirm (F7)</button>
					</div>

					<div class="row">
						<div class="col-6">
							<p v-if="invoiceNumber" class="mb-0"><b>Invoice number</b>: {{ invoiceNumber }}</p>
							<p><b v-if="!invoiceNumber">Current time</b><b v-else>Created time</b>: {{ currentTime }}</p>
							<selector-input :potentialData="types"
								v-model="selectedType" 
								:defaultData="selectedType"
								placeholder="Select type"
								:required="true"
								label="Type"
								name="type"
								:editable="canEdit"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('type')">
							</selector-input>
							<selector-input :potentialData="customers"
								v-model="selectedCustomer" 
								:defaultData="selectedCustomer"
								placeholder="Select customer"
								:required="true"
								label="Customer"
								name="customer_id"
								:editable="canEdit"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('customer_id')"
								v-if="form.type == 'Customer'"
								addon="createCustomer"
								@createCustomer="createCustomer">
							</selector-input>
							<text-input v-model="form.remarks" 
								:defaultValue="form.remarks"
								:required="false"
								type="text"
								label="Remarks"
								name="remarks"
								:editable="canEdit"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('remarks')">
							</text-input>
						</div>
						<div class="col-6">
							<b>Tax:</b> {{ tax | price }}
							<div class="row mt-2">
								<div class="col">
									<text-input v-model="form.discount" 
										:defaultValue="form.discount"
										:required="false"
										type="number"
										label="Discount"
										name="discount"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('discount')">
									</text-input>
								</div>
								<div class="col">
									<selector-input :potentialData="modes"
										v-model="selectedDiscountMode" 
										:defaultData="selectedDiscountMode"
										placeholder="Select discount mode"
										:required="false"
										label="Discount mode"
										name="discount_mode"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('discount_mode')">
									</selector-input>
								</div>
							</div>

							<b>Rounding:</b> RM{{ rounding | price }}<br>
							<b>Total:</b> RM{{ rounded_total | price }}

							<div class="row mt-2" v-if="form.type !== 'Customer'">
								<div class="col">
									<text-input v-model="form.paid" 
										:defaultValue="form.paid"
										:required="true"
										type="number"
										label="Paid"
										name="paid"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('paid')">
									</text-input>
								</div>
								<div class="col">
									<selector-input :potentialData="payment_types"
										v-model="selectedPaymentType" 
										:defaultData="selectedPaymentType"
										placeholder="Select payment type"
										:required="true"
										label="Payment type"
										name="payment_type"
										:editable="canEdit"
										:focus="false"
										:hideLabel="false"
										:error="form.errors.get('payment_type')">
									</selector-input>
								</div>
							</div>

							<div v-if="form.type !== 'Customer'"><b>Change:</b> RM{{ change | price }}</div>
						</div>
				</div>
				
				</div>
				<!-- End main section -->


			</div>

			
		</form>
		<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
		<customers-dialog :data="auth_user" @customerCreated="addCustomer"></customers-dialog>
	</div>
</template>

<script>
	import moment from 'moment';
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
	import ItemRow from "./ItemRow.vue";

	export default {
		props: ['created_by', 'invoice', 'auth_user', 'setting', 'default_product_type'],

		mixins: [ConfirmationMixin],

		components:{ ItemRow},

		data() {
			return {
				form: new Form({
					items: [

					],
					remarks: '',
					subtotal: '',
					discount: 0,
					discount_mode: '%',
					tax: '',
					total: '',
					paid: 0,
					created_by: this.created_by,
					customer_id: '',
					type: 'Cash',
					payment_type: 'Cash',
					type: 'Cash'
				}),

				product_types: [],
				zone_types: [],
				couriers: [],
				products: [],
				types: [
						{label: 'Customer', value: 'Customer'},
						{label: 'Cash', value: 'Cash'},
						{label: 'Credit Card', value: 'Credit Card'}
						],
				payment_types: [
						{label: 'Cash', value: 'Cash'},
						{label: 'Credit Card', value: 'Credit Card'},
						{label: 'Cheque', value: 'Cheque'},
						{label: 'IBG', value: 'IBG'}
						],
				modes: [
					{label: "%", value: "%"},
					{label: "RM", value: "RM"}
				],
				customers: [],

				selectedCustomer: '',
				selectedPaymentType: {label: 'Cash', value: 'Cash'},
				selectedDiscountMode: {label: "%", value: "%"},
				selectedType: {label: 'Cash', value: 'Cash'},

				currentTime: '',
				invoiceNumber: '',
				can_edit_invoice: true
			};
		},

		mounted() {

			this.getProductTypes();
			
			// Time will only move forward if we are creating
			if(!this.invoice) {
				this.currentTime = moment().format('LL LTS');
				setInterval(() => this.updateCurrentTime(), 1000);
			}

			window.addEventListener('keyup', function(event){
	    		if(event.key == "F8" && this.canAddItem) {
	    			// this.toggleAddItem();
	    			this.addItem();
	    		}

	    		if(event.key == "F7") {
	    			// this.toggleAddItem();
	    			this.submit();
	    		}
	    	}.bind(this));

	    	this.addItem();
		},

		methods: {
			getInvoice() {
				axios.get("/invoices/" + this.invoice)
					.then(response => this.setInvoice(response));
			},

			setInvoice(response) {
				let invoice = response.data;

				this.form.items = invoice.items;
				this.selectedType = {label: invoice.type, value: invoice.type};
				this.selectedCustomer = invoice.customer? {label: invoice.customer.name, value : invoice.customer.id } : '';
				this.selectedDiscountMode = {label: invoice.discount_mode, value: invoice.discount_mode};
				this.selectedPaymentType = {label: invoice.payment_type, value: invoice.payment_type};
				this.form.discount_value = invoice.discount_value;
				this.form.paid = invoice.paid;
				this.form.tax = invoice.tax;
				this.form.total = invoice.total;
				this.form.discount = invoice.discount;
				this.form.remarks = invoice.remarks;

				this.can_edit_invoice = invoice.can_edit;

				this.currentTime = invoice.created_at;
				this.invoiceNumber = invoice.invoice_no;
			},

			getProductTypes() {
				axios.get("/data/producttypes")
					.then(response => this.setProductTypes(response))
					.catch(error => this.getProductTypes());
			},

			setProductTypes(response) {
				this.product_types = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;
					obj['has_detail'] = type.has_detail;

					return obj;
				});

				this.getZoneType();
			},

			getZoneType() {
				axios.get("/data/zonetypes")
					.then(response => this.setZoneType(response))
					.catch(error => this.getZoneType());
			},

			setZoneType(response) {
				this.zone_types = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;

					return obj;
				});

				this.getCourier();
			},

			getCourier() {
				axios.get("/data/vendors")
					.then(response => this.setCourier(response))
					.catch(error => this.getCourier());
			},

			setCourier(response) {
				this.couriers = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;
					obj['formula'] = type.formula;

					return obj;
				});

				this.getCustomers();
			},

			getCustomers() {
				axios.get("/customers/list")
					.then(response => this.setCustomers(response))
					.catch(error => this.getCustomers());
			},

			setCustomers(response) {
				this.customers = response.data.map(function(customer){
					let obj = {};

					obj['value'] = customer.id;
					obj['label'] = customer.name;
					obj['type'] = customer.type;

					return obj;
				});

				// this.getDefaultType();
				if(this.invoice) {
					this.getInvoice();
				}
			},

			addItem() {

				this.form.items.push({
					product_id: '',
					courier_id: 0,
					zone_type_id: '',
					product_type_id: this.default_product_type.value,
					zone: '',
					weight: 0,
					description: '',
					tracking_code: '',
					unit: 1,
					price: 0,
					total_price: 0,
					width: 0,
					length: 0,
					height: 0,
					tax: 0,
					tax_rate: 0,
					is_custom_pricing: 0,
					item_tax_inclusive: '',
					dimension_weight: 0,
					sku: '',

				});

				// this.$refs['track_code_' + ( this.form.items.length - 1 )][0].triggerFocus();
			},

			updateItem(event, index) {
				this.form.items[index][event.attribute] = event.value;
			},


			deleteItem(index) {
				// console.log(index);
				this.form.items.splice(index,1);
			},

			updateCurrentTime() {
				this.currentTime = moment().format('LL LTS');
			},

			submit() {
				this.secondary_message = "<div class='d-flex flex-column font-weight-normal'>"
											+ "<div><b>Total: </b> RM" + this.rounded_total.toFixed(2) + "</div>"
											+ "<div><b>Paid: </b> RM" + parseFloat(this.form.paid).toFixed(2) + "</div>"
											+ "<div><b>Change: </b> RM" + this.change.toFixed(2) + "</div>"
											+ "</div>"
				this.isConfirming = true;

			},

			confirmSubmit() {
				this.form.total = this.rounded_total;
				this.form.subtotal = this.subtotal;
				this.form.tax = this.tax;

				let url = this.invoice ? "/invoices/update/" + this.invoice : "/invoices";
				this.form.post(url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				window.open(response.redirect_url, '_blank');

				setInterval(function(){
					window.location.href = "/invoices/create";
				}, 3000);
			},

			createCustomer() {
				window.events.$emit('createCustomer');
			},

			addCustomer(e) {
				let customer = {};

				customer['value'] = e.customer.id;
				customer['label'] = e.customer.name;
				customer['type'] = e.customer.type;

				this.customers.push(customer);

				this.selectedCustomer = customer;
			},

			getPriceForItems(error = 'No error') {

				// console.log(error);
				window.events.$emit('update_price');
			}

		},

		computed: {

			total() {
				// console.log(this.subtotal);
				// console.log(this.tax);
				return parseFloat(this.subtotal) - parseFloat(this.discount_value);
			},

			rounded_total() {
				return this.total + this.rounding;
			},

			rounding() {
				let rounded_total = Math.round(this.total * 100 / 5 ) / 100 * 5;
				let value = this.total - rounded_total;
				// console.log(this.discount_value);
				// console.log(this.total);
				// console.log(value);
				if(value !== 0)
					return value * -1;

				return 0.00;
			},

			subtotal() {
				if(this.form.items.length > 0)
					return _.sumBy(this.form.items, function(item){ return parseFloat(item.total_price); });

				return 0;
			},

			discount_value() {
				let value = this.form.discount ? this.form.discount : 0;

				if(this.form.discount_mode == "%" && this.form.discount)
					value = this.subtotal * parseFloat(this.form.discount) / 100;
				
				return parseFloat(value);
			},

			tax() {
				if( this.form.items.length > 0 )
					return _.sumBy(this.form.items, function(item){ return parseFloat(item.tax); });

				return 0;
			},

			change() {
				if(this.form.paid && this.total)
					return parseFloat(this.form.paid) - this.rounded_total;

				return 0.00;
			},

			tooltip_add() {
				return this.isAddingItem ? "Cancel add item (F8)" : "Add new item (F8)";
			},

			add_button_class() {
				let value = ['text-primary'];

				if(this.isAddingItem) {
					value = ['rotate-45', 'text-danger'];
				}

				if(!this.canAddItem || !this.canEdit) {
					value.push('disabled');
				}

				return value;
			},

			canSubmit() {
				return this.form.items.length > 0 && ( this.selectedCustomer || this.form.paid >= this.rounded_total ) && this.canEdit;
			},

			canEdit() {
				return  ( !this.invoice ||  this.can_edit_invoice ) ;
			},

			canEditItem() {
				return ( !this.invoice ||  this.can_edit_invoice );
			},

			editTooltip() {
				if(!this.canSubmit)
				{
					if(this.invoice && !this.can_edit_invoice)
						return "Invoice has been locked"

					if(this.selectedType.value !== 'Customer' && this.form.paid <= this.rounded_total && this.rounded_total > 0)
						return "Full amount must be paid";

					if(this.selectedType.value == 'Customer' && !this.selectedCustomer)
						return "Customer not selected";

					return "No items";
				}

				return "";
			},

			canAddItem() {
				return this.canEdit;
			}
		},

		watch: {
			selectedType(newVal, oldVal) {
				this.form.type = newVal.value;
				
				if(newVal.value !== 'Customer') {
					this.selectedCustomer = '';
				} 
				if(this.canEdit)
					this.getPriceForItems();
			},

			selectedDiscountMode(newVal, oldVal) {
				this.form.discount_mode = newVal.value;
			},

			selectedPaymentType(newVal, oldVal) {
				this.form.payment_type = newVal.value;
			},

			selectedCustomer(newVal, oldVal) {
				if(newVal)
					this.form.customer_id = newVal.value;
				
				if(this.canEdit)
					this.getPriceForItems();
			}
		}	
	}
</script>