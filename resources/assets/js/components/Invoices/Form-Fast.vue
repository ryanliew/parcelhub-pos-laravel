<template>
	<div>
		<div class="card" id="invoice-header">
			<div class="card-body">
				<div class="row">
					<!-- Start Invoice Section -->
					<div class="col-4">
						<p v-if="invoiceNumber" class="mb-0"><b>Invoice number</b>: {{ invoiceNumber }}</p>
						<p><b v-if="!invoiceNumber">Current time</b><b v-else>Created time</b>: {{ currentTime }}</p>
						<selector-input :potentialData="types"
							v-model="selectedType" 
							:defaultData="selectedType"
							placeholder="Select type"
							:required="true"
							label="Customer type"
							name="type"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
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
							:isHorizontal="true"
							addonTooltip="Create new customer"
							addon="createCustomer"
							@createCustomer="createCustomer">
						</selector-input>
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
							:isHorizontal="true"
							:error="form.errors.get('payment_type')">
						</selector-input>
						<textarea-input v-model="form.remarks" 
							:defaultValue="form.remarks"
							:required="false"
							type="text"
							label="Remarks"
							name="remarks"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('remarks')">
						</textarea-input>

						<div class="text-muted d-flex" style="margin-top: -1rem;">
							<div class="invoice-label"></div>
							<div style="margin-left: -15px" v-if="form.remarks">{{ form.remarks.length }} / 190</div>
						</div>
						<div class="text-muted d-flex">
							<div class="invoice-label"></div>
							<div style="margin-left: -15px"><p class="text-danger" v-if="form.remarks && form.remarks.length > 190">Remarks should not exceed 190 characters</p></div>
						</div>
						
					</div>

					<div class="col-4">
						
						<text-input v-model="form.discount" 
							:defaultValue="form.discount"
							:required="false"
							type="number"
							label="Discount"
							name="discount"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('discount')">
						</text-input>
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
							:isHorizontal="true"
							:error="form.errors.get('discount_mode')">
						</selector-input>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Tax:</b> {{ tax | price }}
						</div>
						<text-input v-model="form.paid" 
							:defaultValue="form.paid"
							:required="true"
							type="number"
							label="Paid"
							name="paid"
							ref="paid"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('paid')"
							@enter="submit"
							:disabled="!canPay">
						</text-input>
					</div>
					<div class="col-4 invoice-summary">
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Items:</b> {{ itemCount }}
						</div>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Rounding:</b> RM{{ rounding | price }}
						</div>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Total:</b><button type="button" class="btn btn-primary" @click="payFull">RM{{ rounded_total | price }} (F6)</button>
						</div>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Change:</b> RM{{ change | price }}
						</div>

						<div class="d-flex justify-content-start">
							<div class="invoice-label"></div>
							<a v-if="selectedCustomer && invoice" target="_blank" :href="'/invoices/do/' + invoice" type="button" class="btn btn-success mr-2">Print delivery note</a>
							<a v-else-if="invoice" target="_blank" :href="'/invoices/receipt/' + invoice" type="button" class="btn btn-success mr-2">Print receipt</a>
							<a v-if="invoice" target="_blank" :href="'/invoices/preview/' + invoice" type="button" class="btn btn-success mr-2">Print invoice</a>
							<button type="button" class="btn btn-primary" :disabled="!canSubmit || !canEdit" :title="editTooltip" @click="submit">Confirm (F7)</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card extra-header" :class="headerClass">
			<div class="card-body">
				<div class="row">
					<!-- Start Invoice Section -->
					<div class="col-4">
						<p v-if="invoiceNumber" class="mb-0"><b>Invoice number</b>: {{ invoiceNumber }}</p>
						<p><b v-if="!invoiceNumber">Current time</b><b v-else>Created time</b>: {{ currentTime }}</p>
						<selector-input :potentialData="types"
							v-model="selectedType" 
							:defaultData="selectedType"
							placeholder="Select type"
							:required="true"
							label="Customer type"
							name="type"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('type')">
						</selector-input>
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
							:isHorizontal="true"
							:error="form.errors.get('payment_type')">
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
						<textarea-input v-model="form.remarks" 
							:defaultValue="form.remarks"
							:required="false"
							type="text"
							label="Remarks"
							name="remarks"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('remarks')">
						</textarea-input>

						<div class="text-muted d-flex" style="margin-top: -1rem;">
							<div class="invoice-label"></div>
							<div style="margin-left: -15px" v-if="form.remarks">{{ form.remarks.length }} / 190</div>
						</div>
						<div class="text-muted d-flex">
							<div class="invoice-label"></div>
							<div style="margin-left: -15px"><p class="text-danger" v-if="form.remarks && form.remarks.length > 190">Remarks should not exceed 190 characters</p></div>
						</div>
					</div>

					<div class="col-4">
						
						<text-input v-model="form.discount" 
							:defaultValue="form.discount"
							:required="false"
							type="number"
							label="Discount"
							name="discount"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('discount')">
						</text-input>
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
							:isHorizontal="true"
							:error="form.errors.get('discount_mode')">
						</selector-input>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Tax:</b> {{ tax | price }}
						</div>
						<text-input v-model="form.paid" 
							:defaultValue="form.paid"
							:required="true"
							type="number"
							label="Paid"
							name="paid"
							:editable="canEdit"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('paid')"
							:disabled="!canPay">
						</text-input>
					</div>
					<div class="col-4 invoice-summary">
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Items:</b> {{ itemCount }}
						</div>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Rounding:</b> RM{{ rounding | price }}
						</div>
						<div class="d-flex align-items-center mb-3">
							<b class="invoice-label text-right">Total:</b><button type="button" class="btn btn-primary" @click="payFull">RM{{ rounded_total | price }} (F6)</button>
						</div>
						<div class="d-flex align-items-center mb-3" v-if="form.type !== 'Customer'">
							<b class="invoice-label text-right">Change:</b> RM{{ change | price }}
						</div>
						<div class="d-flex justify-content-start">
							<div class="invoice-label"></div>
							<a v-if="selectedCustomer && invoice" target="_blank" :href="'/invoices/do/' + invoice" type="button" class="btn btn-success mr-2">Print delivery note</a>
							<a v-else-if="invoice" target="_blank" :href="'/invoices/receipt/' + invoice" type="button" class="btn btn-success mr-2">Print receipt</a>
							<a v-if="invoice" target="_blank" :href="'/invoices/preview/' + invoice" type="button" class="btn btn-success mr-2">Print invoice</a>
							<button class="btn btn-primary" :disabled="!canSubmit || !canEdit" :title="editTooltip" @click="submit">Confirm (F7)</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card mt-3">
			<div class="card-body">
				
				<div class="invoice-items">
					<div class="header">Nr:</div>
					<div class="header">Product type:</div>
					<div class="header">Zone type:</div>
					<div class="header">Zone:</div>
					<div class="header">Weight(kg):</div>
					<div class="header">Dim wt(kg):</div>
					<div class="header">Courier:</div>
					<div class="header">SKU:</div>
					<div class="header">Description:</div>
					<div class="header">Unit:</div>
					<div class="header">Track code:</div>
					<div class="header">Price:</div>
					<div class="header"><i class="fa fa-exclamation-circle text-white pl-1"></i></div>
					<div class="header">Total price:</div>
					<div class="header"><button type="button" class="btn btn-sm btn-primary mb-3" @click="calcParcelsPrice">Check price</button></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"></div>
					<div class="header"><button type="button" class="btn btn-sm btn-primary mb-3" @click="addItem" :disabled="!canAddItem">Add Item (F8)</button></div>
				</div>
				<template v-for="(item, index) in form.items" v-if="!item.is_deleted">
					<item-row :isEdit="is_edit" :items="form.items" :index="index" :canEdit="canEditItem" :item="item" :product_types="product_types" :zone_types="zone_types" :couriers="couriers" :defaultProductType="default_product_type" :selectedType="selectedType" :selectedCustomer="selectedCustomer" @delete="deleteItem(index)" @update="updateItem($event, index)" @addItem="addItem" @mass="massInput(index)"></item-row>
				</template>
			</div>
		</div>
		<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" :hideconfirm="isHideConfirm" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
		<customers-dialog :data="auth_user" @customerCreated="addCustomer"></customers-dialog>

		<modal :active="isMassInput"
			id="mass-input-dialog"
			@close="closeMassInput">

			<span slot="header">Enter tracking numbers</span>

			<text-input v-model="mass_tracking_no" 
				:defaultValue="mass_tracking_no"
				:required="true"
				type="text"
				label="Tracking number"
				name="tracking_number"
				ref="tracking_number"
				:editable="true"
				:focus="false"
				:hideLabel="false"
				@enter="checkTrackingNumber">
			</text-input>

			<ol>
				<li v-for="tracking in trackings">
					{{ tracking }}
				</li>
			</ol>

			<template slot="footer">
				<button type="button" class="btn btn-secondary" @click="closeMassInput">Cancel</button>
				<button type="button" class="btn btn-primary" @click="confirmTrackingNumber">Confirm</button>
			</template>
		</modal>
	</div>
</template>

<script>
	import moment from 'moment';
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
	import ItemRow from "./ItemRow.vue";

	export default {
		props: ['created_by', 'invoice', 'auth_user', 'setting', 'default_product_type', 'is_edit'],

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
					type: 'Cash',
					discount_value: 0, 
				}),

				product_types: [],
				zone_types: [],
				couriers: [],
				products: [],
				types: [
						{label: 'Customer', value: 'Customer'},
						{label: 'Cash', value: 'Cash'}
						],
				payment_types: [
						{label: 'Cash', value: 'Cash'},
						{label: 'Credit Card', value: 'Credit Card'},
						{label: 'Cheque', value: 'Cheque'},
						{label: 'GrabPay', value: 'GrabPay'},
						{label: 'IBG', value: 'IBG'},
						{label: 'Others', value: 'Others'}
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
				can_edit_invoice: true,

				headerTop: 0,
				headerClass: '',

				isMassInput: false,
				mass_tracking_no: '',
				trackings: [],
				mass_input_target: '', 

				isHideConfirm: false,
			};
		},

		mounted() {

			this.headerTop = document.getElementById('invoice-header').offsetTop;
			this.getProductTypes();

			window.addEventListener('scroll', this.adjustHeader);
			
			// Time will only move forward if we are creating
			if(!this.invoice) {
				this.currentTime = moment().format('LL LTS');
				setInterval(() => this.updateCurrentTime(), 1000);
			}

			window.addEventListener('keydown', function(event){
	    		if(event.key == "F8" && this.canAddItem) {
	    			// this.toggleAddItem();
	    			this.addItem();
	    		}

	    		if(event.key == "F7") {
	    			if(this.canSubmit)
	    				this.submit();
	    			else
	    				this.$refs.paid.triggerFocus();
	    		}

	    		if(event.key == "F6") {
	    			// console.log("Clicked!");
	    			event.preventDefault();
	    			this.payFull();
	    		}
	    	}.bind(this));
		},

		methods: {
			payFull() {
				if(this.canPay)
					this.form.paid = this.rounded_total;
			},

			doNothing() {
				// console.log("Do nothing");
				// This method does nothing
				// This is on purpose such that the Enter key will not trigger invoice creation
			},

			adjustHeader(event) {
				
				if(document.documentElement.scrollTop > this.headerTop) {
					this.headerClass = "header-fixed";
				} else {
					this.headerClass = "";
				}
			},

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
				// console.log(invoice.payment_type);
				this.selectedPaymentType = {label: invoice.payment_type, value: invoice.payment_type};
				this.form.discount_value = invoice.discount_value;
				this.form.paid = invoice.paid;
				this.form.tax = invoice.tax;
				this.form.total = invoice.total.toFixed(2);
				this.form.discount = invoice.discount.toFixed(2);
				this.form.remarks = invoice.remarks;

				this.can_edit_invoice = invoice.can_edit;

				this.currentTime = invoice.created_at;
				this.invoiceNumber = invoice.invoice_no;

				
				Vue.nextTick( () => window.events.$emit("updateItemsValue") );
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

			getCustomers(error = 'No error') {
				// console.log(error);
				axios.get("/customers/list")
					.then(response => this.setCustomers(response))
					.catch(error => this.getCustomers(error));
			},

			setCustomers(response) {
				this.customers = response.data.map(function(customer){
					let obj = {};

					obj['value'] = customer.id;
					obj['label'] = customer.name;
					obj['type'] = customer.type;
					obj['customer_group_id'] = customer.customer_group_id;

					return obj;
				});


	    		this.addItem();
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
					tax_type: 'SR',
					shouldFocus: true,
					has_error: false,
					is_deleted: false, // A flag to determine if we have deleted this item
					default_details: true // A flag that determines if we should go get the default details for this item row

				});



				// this.$refs['track_code_' + ( this.form.items.length - 1 )][0].triggerFocus();
			},

			updateItem(event, index) {
				// console.log("Index: " + index + " Courier:" + this.form.items[index].courier_id);
				this.form.items[index][event.attribute] = event.value;
			},


			deleteItem(index) {
				// console.log(index);
				// this.form.items.splice(index,1);
				this.form.items[index].is_deleted = true;
				// Vue.nextTick( () => window.events.$emit("updateItemsValue") );
			},

			deleteMassItem(index) {
				this.form.items[index].is_deleted = true;
				Vue.nextTick( () => window.events.$emit("updateItemsValue") );
			},

			updateCurrentTime() {
				this.currentTime = moment().format('LL LTS');
			},

			submit() {
				if(this.canSubmit) {
					// console.log("Submitting");
					this.form.items = _.filter(this.form.items, function(item){ return item.product_id && !item.is_deleted ? true : false; });		
					let url = "/parcels/validate";
					this.form.post(url)
						.then(response => this.onSuccessValidate(response))
						.catch(error => this.onErrorValidate(error));
					}				
			},

			confirmSubmit() {				
				this.form.total = this.rounded_total;
				this.form.subtotal = this.subtotal;
				this.form.tax = this.tax;
				this.form.discount_value = this.discount_value;

				let url = this.invoice ? "/invoices/update/" + this.invoice : "/invoices";
				this.form.post(url)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccessValidate(response)
			{	
				this.isHideConfirm = false;
				let validate_message = "Are you sure?"
				
				if(!response.is_valid){
					validate_message = "<div class='alert alert-danger error-message'>"
										+ "<div>" + response.message + "</div>"
										+ "</div>"
					this.isHideConfirm = true;
				}	
				this.confirm_message = validate_message
				this.secondary_message = "<div class='d-flex flex-column font-weight-normal'>"
											+ "<div><b>Total: </b> RM" + this.rounded_total.toFixed(2) + "</div>"
											+ "<div><b>Paid: </b> RM" + parseFloat(this.form.paid).toFixed(2) + "</div>"
											+ "<div><b>Change: </b> RM" + this.change.toFixed(2) + "</div>"
											+ "</div>"
				this.isConfirming = true;	
			},

			onErrorValidate(response)
			{
				this.isConfirming = false;
			},


			onSuccess(response) {
				console.log("Success");
				window.open(response.redirect_url, '_blank');

				setInterval(function(){
					window.location.href = "/invoices/create";
				}, 3000);
			},

			onError(error) {
				
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
			},

			massInput(index) {
				this.mass_input_target = index;
				this.isMassInput = true;

				setTimeout(function(){ this.$refs.tracking_number.triggerFocus() }.bind(this), 500 );
			},

			closeMassInput() {
				this.isMassInput = false;
				this.trackings = [];
			},

			checkTrackingNumber(){
				if(!this.mass_tracking_no)
					this.confirmTrackingNumber();
				else
					this.addTrackingNumber();
			},

			addTrackingNumber() {
				this.trackings.push(this.mass_tracking_no);
				this.mass_tracking_no = '';
			},

			confirmTrackingNumber() {
				
				this.trackings.forEach(function(tracking, key){

						let newItem = JSON.parse(JSON.stringify(this.form.items[this.mass_input_target]));
						// console.log(newItem);
						newItem['tracking_code'] = tracking;
						newItem['shouldFocus'] = false;
						newItem['has_error'] = false;
						newItem['unit'] = 1;
						newItem['is_deleted'] = false;
						newItem['default_details'] = false; // Should not get default details for this row
						// console.log(newItem.tracking_code);
						this.form.items.push(newItem);

				}.bind(this));

				this.isMassInput = false;
				this.trackings = [];


				this.deleteMassItem(this.mass_input_target);
				Vue.nextTick( () => this.addItem() );
			}, 

			calcParcelsPrice() {
				let url = "/parcels/charge";
				this.form.post(url)
					.then(response => this.onSuccessGetParcelsPrice(response))
					.catch(error => this.onError(error));
			},

			onSuccessGetParcelsPrice(response){
			    response.return_items.forEach(function(response_item){
					this.form.items.forEach(function(item, index){
						if(item.tracking_code == response_item.tracking_code) 
						{
							this.form.items[index]["price"] = response_item.charge;
						}
					}.bind(this));
				}.bind(this));
				
				window.events.$emit("updateItemsValue");
			}
		},

		computed: {

			total() {
				// console.log(this.subtotal);
				// console.log(this.tax);
				return parseFloat(this.subtotal) - parseFloat(this.discount_value);
			},

			rounded_total() {
				return Math.round((this.total + this.rounding) * 100) / 100;
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
					return _.sumBy(this.form.items, function(item){ return item.is_deleted ? 0 : parseFloat(item.total_price); });

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
					return _.sumBy(this.form.items, function(item){ return item.is_deleted ? 0 : parseFloat(item.tax); });

				return 0;
			},

			change() {
				if(this.form.paid && this.total)
					return parseFloat(this.form.paid) - this.rounded_total < 0.01 ? 0.00 : parseFloat(this.form.paid) - this.rounded_total;

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

			itemCount() {
				return _.filter(this.form.items, function(item){ return item.product_id && !item.is_deleted ? true : false; }).length;
			},

			canSubmit() {
				return this.itemCount > 0 
						&& ( this.selectedType.value !== 'Customer' || ( this.selectedType.value == 'Customer' && this.selectedCustomer )) 
						&& ( this.selectedPaymentType.value.toLowerCase() == 'account' || this.form.paid >= this.rounded_total ) 
						&& !_.find(this.form.items, function(item){ return item.is_deleted ? false : item.has_error; })
						&& (!this.form.remarks || this.form.remarks.length <= 190)
						&& this.canEdit;
			},

			canEdit() {
				return  ( !this.invoice ||  this.can_edit_invoice );
			},

			canEditItem() {
				return ( !this.invoice ||  this.can_edit_invoice );
			},

			editTooltip() {
				if(!this.canSubmit)
				{
					if(this.invoice && !this.can_edit_invoice)
						return "Invoice has been locked"

					if(this.selectedType.value !== 'Customer' && this.canPay && this.form.paid < this.rounded_total && this.rounded_total > 0)
						return "Full amount must be paid";

					if(this.selectedType.value == 'Customer' && !this.selectedCustomer)
						return "Customer not selected";

					if(this.form.remarks && this.form.remarks.length > 190)
						return "Remarks exceed 190 characters";

					if(_.find(this.form.items, function(item){ return item.is_deleted ? false : item.has_error })) 
						return "Items detail incomplete";

					return "No items";
				}

				return "";
			},

			canAddItem() {
				return this.canEdit;
			},

			canPay() {
				return true;
				// return this.selectedPaymentType.value.toLowerCase() !== 'account'; Everyone can pay now
			}
		},

		watch: {
			selectedType(newVal, oldVal) {
				this.form.type = newVal.value;
				
				if(!this.is_edit) {
					if(newVal.value !== 'Customer') {
						this.payment_types = [
							{label: 'Cash', value: 'Cash'},
							{label: 'Credit Card', value: 'Credit Card'},
							{label: 'Cheque', value: 'Cheque'},
							{label: 'GrabPay', value: 'GrabPay'},
							{label: 'IBG', value: 'IBG'},
							{label: 'Others', value: 'Others'}
						];
						this.selectedCustomer = '';
						this.selectedPaymentType = {value: 'Cash', label: 'Cash'};
					} else {
						this.payment_types = [
							{label: 'Account', value: 'Account'},
							{label: 'Cash', value: 'Cash'},
							{label: 'Credit Card', value: 'Credit Card'},
							{label: 'Cheque', value: 'Cheque'},
							{label: 'GrabPay', value: 'GrabPay'},
							{label: 'IBG', value: 'IBG'},
							{label: 'Others', value: 'Others'}
						];

						this.selectedPaymentType = {value: 'Account', label: 'Account'};
					}
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