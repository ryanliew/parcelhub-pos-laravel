<template>
	<div>
		<form @submit.prevent="submit" 
			@keydown="form.errors.clear($event.target.name)" 
			@input="form.errors.clear($event.target.name)"
			@keyup.enter="moveToNext">
			<div class="row">
				<!-- Start main section -->
				<div class="col-5 invoice-right">
					<p><b>Current time</b>: {{ currentTime }}</p>
					<div class="row">
						<div class="col-5 small-select">
							<selector-input :potentialData="types"
								v-model="selectedType" 
								:defaultData="selectedType"
								placeholder="Select type"
								:required="true"
								label="Type"
								name="type"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('type')">
							</selector-input>
						</div>
						<div class="col">
							<text-input v-model="form.remarks" 
								:defaultValue="form.remarks"
								:required="false"
								type="text"
								label="Remarks"
								name="remarks"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('remarks')">
							</text-input>
						</div>
					</div>
					<selector-input :potentialData="customers"
						v-model="selectedCustomer" 
						:defaultData="selectedCustomer"
						placeholder="Select customer"
						:required="true"
						label="Customer"
						name="customer_id"
						:editable="true"
						:focus="false"
						:hideLabel="false"
						:error="form.errors.get('customer_id')"
						v-if="form.type == 'Customer'"
						addon="createCustomer"
						@createCustomer="createCustomer">
					</selector-input>
					

					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr class="small-text">
									<th>Tracking code</th>
									<th>Item</th>
									<th>Unit</th>
									<th>Price</th>
									<th>
										<span class="fa-stack pointer transition-ease" :class="add_button_class" :title="tooltip_add" @click="toggleAddItem" :disabled="!canAddItem">
											<i class="fas fa-circle fa-stack-2x"></i>
											<i class="fas fa-plus fa-stack-1x fa-inverse text-white" v-if="canAddItem"></i>
											<i class="fas fa-stack-1x fa-inverse text-white fa-circle-notch fa-spin" v-else></i>
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in form.items" class="transition-ease small-text" :class="getItemRowClass(index)">
									<td>{{ item.tracking_code }}</td>
									<td>{{ item.description }}</td>
									<td>{{ item.unit }}</td>
									<td>{{ item.total_price | price }}</td>
									<td><i class="fas fa-edit text-primary pointer" @click="editItem(index)"></i> <i class="fas fa-times text-danger pointer" @click="deleteItem(index)"></i></td>
								</tr>
							</tbody>
							<tfoot v-if="form.items.length > 0">
								<tr class="small-padding">
									<td colspan="3" class="text-right"><b>Subtotal:</b></td>
									<td>{{ subtotal | price }}</td>
									<td></td>
								</tr>
								<tr class="small-padding">
									<td colspan="3" class="text-right"><b>Tax:</b></td>
									<td>{{ tax | price }}</td>
									<td></td>
								</tr>
								<tr class="small-padding">
									<td colspan="3" class="text-right"><b>Rounding:</b></td>
									<td>{{ rounding | price }}</td>
									<td></td>
								</tr>
							</tfoot>	
						</table>
					</div>

					<div class="row">
						<div class="col">
							<text-input v-model="form.discount" 
								:defaultValue="form.discount"
								:required="false"
								type="number"
								label="Discount"
								name="discount"
								:editable="true"
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
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('discount_mode')">
							</selector-input>
						</div>
					</div>
					<div class="row" v-if="form.type !== 'Customer'">
						<div class="col-7">
							<selector-input :potentialData="payment_types"
								v-model="selectedPaymentType" 
								:defaultData="selectedPaymentType"
								placeholder="Select payment type"
								:required="true"
								label="Payment type"
								name="payment_type"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('payment_type')">
							</selector-input>
						</div>
						<div class="col">
							<text-input v-model="form.paid" 
								:defaultValue="form.paid"
								:required="true"
								type="number"
								label="Paid"
								name="paid"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="form.errors.get('paid')">
							</text-input>
						</div>
					</div>

					<h4 class="text-right">Discount: RM{{ discount_value | price }}</h4>

					<h4 class="text-right">Total: RM{{ rounded_total | price }}</h4>

					<h4 class="text-right" v-if="form.type !== 'Customer'">Change: RM{{ change | price }}</h4>

					<div class="d-flex justify-content-end">
						<a v-if="selectedCustomer && invoice" target="_blank" :href="'/invoices/do/' + invoice" type="button" class="btn btn-success mr-2">Print delivery note</a>
						<a v-else-if="invoice" target="_blank" :href="'/invoices/receipt/' + invoice" type="button" class="btn btn-success mr-2">Print receipt</a>
						<a v-if="invoice" target="_blank" :href="'/invoices/preview/' + invoice" type="button" class="btn btn-success mr-2">Print invoice</a>
						<button type="submit" class="btn btn-primary" :disabled="!canEdit || isLoading" :title="editTooltip">Confirm</button>
					</div>
				</div>
				<!-- End main section -->

				<!-- Start item section -->
				<transition name="left-slide">
					<div class="col-7 invoice-left" v-if="isAddingItem">
					<text-input v-model="tracking_no" 
						:defaultValue="tracking_no"
						:required="this.selectedProductType.has_detail"
						type="text"
						label="Tracking no"
						name="tracking_no"
						:editable="true"
						:focus="true"
						:hideLabel="false"
						:error="tracking_no_error"
						ref="tracking_input">
					</text-input>
					<selector-input :potentialData="product_types"
						v-model="selectedProductType" 
						:defaultData="selectedProductType"
						placeholder="Select product type"
						:required="true"
						label="Product type"
						name="product_type"
						:editable="true"
						:focus="false"
						:hideLabel="false"
						:error="selectedProductType_error"
						ref="producttypes">
					</selector-input>
					<div class="row">
						<div class="col">
							<selector-input :potentialData="zone_types"
								v-model="selectedZoneType" 
								:defaultData="selectedZoneType"
								placeholder="Select zone type"
								:required="false"
								label="Zone type"
								name="zone_type"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="selectedZoneType_error"
								v-if="isParcelOrDocument">
							</selector-input>
						</div>
						<div class="col">
							<selector-input :potentialData="couriers"
								v-model="selectedCourier" 
								:defaultData="selectedCourier"
								placeholder="Select courier"
								:required="false"
								label="Courier"
								name="courier"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="selectedCourier_error"
								v-if="isParcelOrDocument">
							</selector-input>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<text-input v-model="zone" 
								:defaultValue="zone"
								:required="true"
								type="number"
								label="Zone"
								name="zone"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="zone_error"
								step="1"
								v-if="isParcelOrDocument">
							</text-input>
						</div>
						<div class="col">
							<text-input v-model="weight" 
								:defaultValue="weight"
								:required="true"
								type="number"
								label="Weight (KG)"
								name="weight"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="weight_error"
								v-if="isParcelOrDocument">
							</text-input>
						</div>
						<div class="col">
							<text-input v-model="dimension_weight" 
								:defaultValue="dimension_weight"
								:required="false"
								type="number"
								label="Dimension weight (KG)"
								name="dimension_weight"
								:editable="true"
								:focus="false"
								:hideLabel="false"
								:error="dimension_weight_error"
								v-if="isParcelOrDocument"
								addon="Calculate"
								@addon="isCalculatingDimWeight = true">
							</text-input>
						</div>
					</div>
					<selector-input :potentialData="products"
						v-model="selectedProduct" 
						:defaultData="selectedProduct"
						placeholder="Select SKU"
						:required="true"
						label="SKU"
						name="product"
						:editable="true"
						:focus="false"
						:hideLabel="false"
						:error="selectedProduct_error"
						@input="productChange">
					</selector-input>
					
					<div class="row">
						<div class="col-4">
							<text-input v-model="description" 
								:defaultValue="description"
								:required="false"
								type="text"
								label="Description"
								name="description"
								:editable="false"
								:focus="false"
								:hideLabel="false"
								:error="description_error">
							</text-input>
						</div>
						<div class="col">
							<text-input v-model="unit" 
								:defaultValue="unit"
								:required="true"
								type="number"
								label="Unit"
								name="unit"
								:editable="false"
								:focus="false"
								:hideLabel="false"
								:error="unit_error">
							</text-input>
						</div>
						<div class="col">
							<text-input v-model="price" 
								:defaultValue="price"
								:required="true"
								type="number"
								label="Price"
								name="price"
								:editable="selectedProductType.value == 1"
								:focus="false"
								:hideLabel="false"
								:error="price_error">
							</text-input>
						</div>
						<div class="col">
							<text-input v-model="item_tax" 
								:defaultValue="item_tax"
								:required="true"
								type="number"
								label="Tax"
								name="item_tax"
								:editable="false"
								:focus="false"
								:hideLabel="false"
								:error="item_tax_error">
							</text-input>
						</div>
						<div class="col">
							<text-input v-model="total_price" 
								:defaultValue="total_price"
								:required="true"
								type="number"
								label="Total price"
								name="total_price"
								:editable="false"
								:focus="false"
								:hideLabel="false">
							</text-input>
						</div>
					</div>

					<button type="button" class="btn btn-primary" @click="add_item" :disabled="!canEditItem">Confirm</button>
					<button type="button" class="btn btn-secondary" @click="toggleAddItem">Cancel</button>
					</div>
				</transition>	
				<!-- End item section -->
			</div>

			<modal :active="isCalculatingDimWeight"
				id="dim-weight-calculator"
				@close="isCalculatingDimWeight = false">

				<span slot="header">Calculate dimension weight</span>

				<text-input v-model="height" 
					:defaultValue="height"
					:required="true"
					type="number"
					label="Height (cm)"
					name="height"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('height')">
				</text-input>
				<text-input v-model="width" 
					:defaultValue="width"
					:required="true"
					type="number"
					label="Width (cm)"
					name="width"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('width')">
				</text-input>
				<text-input v-model="length" 
					:defaultValue="length"
					:required="true"
					type="number"
					label="Length (cm)"
					name="length"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('length')">
				</text-input>

				<template slot="footer">
					<button type="button" class="btn btn-secondary" @click="isCalculatingDimWeight = false">Cancel</button>
					<button type="button" class="btn btn-primary" @click="calculateDimWeight">Confirm</button>
				</template>
			</modal>
		</form>
		<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
		<customers-dialog :data="auth_user" @customerCreated="addCustomer"></customers-dialog>
	</div>
</template>

<script>
	import moment from 'moment';
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";

	export default {
		props: ['created_by', 'invoice', 'auth_user', 'setting'],

		mixins: [ConfirmationMixin],

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

				isAddingItem: false,
				isEditing: false,
				editingIndex: '',
				isLoading: true,

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

				defaultProductType: '',
				isCalculatingDimWeight: false,
				tracking_no: '',
				selectedProductType: '',
				selectedZoneType: '',
				zone: '',
				weight: '',
				dimension_weight: 0,
				selectedCourier: '',
				selectedProduct: '',
				description: '',
				price: '',
				unit: 1,
				width: 0,
				length: 0,
				height: 0,
				item_tax: 0,

				tracking_no_error: '',
				selectedProductType_error: '',
				selectedZoneType_error: '',
				zone_error: '',
				weight_error: '',
				dimension_weight_error: '',
				selectedCourier_error: '',
				selectedProduct_error: '',
				description_error: '',
				price_error: '',
				unit_error: '',
				item_tax_error: '',

				currentTime: '',

				item_add_loading: false,
				price_group: '',
				can_edit_invoice: true
			};
		},

		mounted() {
			if(this.invoice) {
				this.getInvoice();
			}

			this.getProductTypes();
			this.currentTime = moment().format('LL LTS');
			setInterval(() => this.updateCurrentTime(), 1000);

			window.addEventListener('keyup', function(event){
				console.log("Keyup" + event.key);

	    		if(event.key == "F8" && this.canAddItem) {
	    			this.toggleAddItem();
	    		}
	    	}.bind(this));
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
			},

			moveToNext() {
				this.$refs.producttypes.focus();
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

				this.selectedProductType = _.filter(this.product_types, function(type){ return type.value == this.defaultProductType; }.bind(this));
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

				this.getDefaultType();
			},

			getRelatedProduct(error = 'No error'){
				if(this.selectedProductType) {
					axios.get('/data/products?type=' + this.selectedProductType.value)
						.then(response => this.setProduct(response))
						.catch(error => this.getRelatedProduct(error));

					this.isLoading = false;
					if(this.selectedProductType.has_detail && !this.isEditing) {
						this.getDefaultDetails();
					}
				}
			},

			getDefaultDetails() {
				axios.get("/data/branch/knowledge?type=" + this.selectedProductType.label)
					.then(response => this.setDefaultDetails(response))
					.catch(error => this.getDefaultDetails());
			},

			setDefaultDetails(response) {

				let eligibleZoneTypes = _.filter(this.zone_types, function(type){ return type.label == response.data.result.zone_type; }.bind(response));

				if(eligibleZoneTypes.length > 0)
				{
					this.selectedZoneType = eligibleZoneTypes[0];
				}

				let eligibleCourier = _.filter(this.couriers, function(courier){ return courier.label == response.data.result.vendor_name; }.bind(response));
				
				if(eligibleCourier.length > 0)
				{
					this.selectedCourier = eligibleCourier[0];
				}
			},

			getDefaultType() {
				axios.get("/data/branch/type")
					.then(response => this.setDefaultType(response))
					.catch(error => this.getDefaultProductType());
			},

			setDefaultType(response) {
				this.defaultProductType = response.data.type;
				this.selectedProductType = _.filter(this.product_types, function(type){ return type.value == response.data.type; }.bind(response))[0];

				this.getRelatedProduct();
			},

			getFilteredProduct() {
				if(this.zone && (this.weight || this.dimension_weight) && this.selectedCourier) {
					let url = "/data/products?type=" + this.selectedProductType.value + "&zone=" + this.zone;

					if(this.weight)
						url += "&weight=" + this.weight;

					if(this.dimension_weight)
						url += "&dimension=" + this.dimension_weight;

					if(this.selectedCourier)
						url += "&vendor=" + this.selectedCourier.value;

					axios.get(url)
						.then(response => this.setProduct(response))
						.catch(error => this.getFilteredProduct());
				}
			},

			setProduct(response) {
				if(!this.isEditing) this.selectedProduct = '';
				this.selectedProduct_error = "";
				this.products = response.data.map(function(product){
					let obj = {};

					obj['value'] = product.id;
					obj['label'] = product.sku;
					obj['description'] = product.description;


					return obj;
				}.bind(this));

				// If we only have 1 product, set it as default
				if(this.products.length == 1) {
					this.selectedProduct = this.products[0];
				}
				// If we dont have any products that matches
				if(this.products.length == 0) {
					this.selectedProduct_error = "No matching SKU found";
				}

				// If selected product type = Document or Parcel
				if(this.selectedProductType.value == 2 || this.selectedProductType.value == 5)
				{
					this.selectedZoneType = {label: 'Domestic', value: 1};
					if(this.products.length > 1) {
						this.getFilteredProduct();
					}
				}
			},

			productChange() {
				this.description = "";
				this.price = "";
				this.item_tax = 0;
				if(this.selectedProduct) {
					this.description = this.selectedProduct.description;

					this.getProductPrice();

					
				}
			},

			getProductPriceUrl(product) {

				let url = "/data/pricing?product=" + product;
				if(this.selectedType.label == "Customer" && this.selectedCustomer) {
					url += "&customer=" + this.selectedCustomer.value;
				}

				return url;
			},

			getProductPrice(error = 'No error') {
				console.log(error);
				if(this.selectedProduct) {
					this.item_add_loading = true;

					let url = this.getProductPriceUrl(this.selectedProduct.value);
					
					axios.get(url)
						.then(response => this.setProductPrice(response))
						.catch(error => this.getProductPrice(error));
				}

				this.setProductPrice('');
			},

			calculatePriceBasedOnCustomer(price_group) {
				let price = price_group.walk_in_price;

				if(this.selectedType.label == "Customer" && this.selectedCustomer)
				{
					if(this.selectedCustomer.type == 'walk_in_special')
						price = price_group.walk_in_price_special;
					else if(this.selectedCustomer.type == 'Corporate')
						price = price_group.corporate_price;
				}

				let tax = price_group.tax ? price * price_group.tax : 0;
				let total = price + tax;

				return {price: price, tax: tax, total: total};
			},

			setProductPrice(response) {
				// console.log("Setting product price");
				if(response) {
					this.price_group = this.selectedProduct;

					if(response.data)
						this.price_group = response.data;

					let prices = this.calculatePriceBasedOnCustomer(this.price_group)
					this.price = prices.price;
					this.item_tax = prices.tax;

					this.price = this.price.toFixed(2);
					this.item_tax = this.item_tax.toFixed(2);
				}

				this.item_add_loading = false;
			},

			calculateDimWeight() {
				let formula = this.selectedCourier.formula;

				let expression = formula.replace("l", this.length);
				expression = expression.replace("w", this.width);
				expression = expression.replace("h", this.height);

				this.dimension_weight = eval(expression);
				this.isCalculatingDimWeight = false;
			},

			add_item() {
				if(this.validateInputs())
				{
					let item = {};

					item['tracking_code'] = this.tracking_no;
					item['description'] = this.description;
					item['zone'] = this.zone;
					item['weight'] = this.weight ? this.weight : 0;
					item['dimension_weight'] = this.dimension_weight ? this.dimension_weight : 0; 
					item['height'] = this.height ? this.height : 0;
					item['length'] = this.length ? this.length : 0;
					item['width'] = this.width ? this.width : 0;
					item['sku'] = this.selectedProduct.label;
					item['tax'] = this.item_tax;
					item['price'] = this.price;
					item['courier_id'] = this.selectedCourier.value;
					item['product_id'] = this.selectedProduct.value;
					item['product_type_id'] = this.selectedProductType.value;
					item['total_price'] = this.total_price;
					item['unit'] = this.unit;

					if(this.isEditing) {
						Vue.set(this.form.items, this.editingIndex, item);
						// this.form.items[this.editingIndex] = item;
						this.editingIndex = '';
					}
					else {
						this.form.items.push(item);
					}

					this.selectedZoneType = '';
					this.zone = '';
					this.weight = '';
					this.dimension_weight = 0;
					this.selectedCourier = '';
					this.selectedProduct = {label: 'Packaging', value: 4};
					this.selectedProductType = 
					this.description = '';
					this.price = '';
					this.unit = 1;
					this.height = 0;
					this.width = 0;
					this.length = 0;
					this.tracking_no = '';
					this.item_tax = 0;

					this.toggleAddItem();
				}
			},

			editItem(index) {

				this.isEditing = true;
				this.editingIndex = index;

				this.toggleAddItem();

				let item = this.form.items[index];


				this.selectedZoneType = this.zone_types[0];
				this.selectedCourier = item.courier_id ? _.filter(this.couriers, function(courier){ return item.courier_id == courier.value; }.bind(item))[0] : '';
				this.selectedProductType = _.filter(this.product_types, function(type){ return item.product_type_id == type.value; }.bind(item))[0];

				this.selectedProduct = {value: item.product_id, label: item.sku, description: item.description};
				// this.selectedProduct = _.filter(this.products, function(product){ return item.product_id == product.value; }.bind(item))[0];
				this.zone = item.zone;
				this.weight = item.weight;
				this.dimension_weight = item.dimension_weight;
				this.description = item.description;
				this.price = item.price;
				this.unit = item.unit;
				this.height = item.height;
				this.width = item.width;
				this.length = item.length;
				this.tracking_no = item.tracking_code;
				this.item_tax = item.tax;

			},

			deleteItem(index) {
				this.form.items.splice(index,1);
			},

			validateInputs() {
				this.tracking_no_error = this.tracking_no || !this.selectedProductType.has_detail ? '' : 'This field is required';
				this.selectedProductType_error = this.selectedProductType ? '' : 'This field is required';
				this.selectedZoneType_error = this.selectedZoneType || !this.isParcelOrDocument ? '' : 'This field is required';
				this.zone_error = this.zone || !this.isParcelOrDocument ? '' : 'This field is required';
				this.weight_error = this.weight || !this.isParcelOrDocument ? '' : 'This field is required';
				// this.dimension_weight_error = this.dimension_weight || !this.isParcelOrDocument ? '' : 'This field is required';
				this.selectedCourier_error = this.selectedCourier || !this.isParcelOrDocument ? '' : 'This field is required';
				this.selectedProduct_error = this.selectedProduct ? '' : 'This field is required';

				return this.tracking_no_error == "" && this.selectedProductType_error == "" && this.selectedZoneType_error == "" && this.zone_error == "" && this.weight_error == "" && this.dimension_weight_error == "" && this.selectedCourier_error == "" && this.selectedProduct_error == "" && this.description_error == "" && this.price_error == "" && this.unit_error == "";
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

			toggleAddItem() {
				this.selectedZoneType = '';
				this.zone = '';
				this.weight = '';
				this.dimension_weight = 0;
				this.selectedCourier = '';
				this.selectedProduct = '';
				this.selectedProductType = _.filter(this.product_types, function(type){ return type.value == this.defaultProductType; }.bind(this))[0];
				this.description = '';
				this.price = '';
				this.unit = 1;
				this.height = '';
				this.width = '';
				this.length = '';
				this.tracking_no = '';

				this.getRelatedProduct();

				this.isAddingItem = !this.isAddingItem;

				if(!this.isAddingItem && this.isEditing) 
				{
					this.isEditing = false;
					this.editingIndex = '';
				}

			},

			getItemRowClass(index) {
				return this.editingIndex == index && this.isEditing ? "item-editing" : '';
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
				this.isLoading = this.form.items.length > 0;
				this.form.items.forEach(function(element, index) {
					let url = this.getProductPriceUrl(element.product_id);
					
					axios.get(url)
						.then(response => this.changePriceForItem(index, response.data))
						.catch(error => this.getPriceForItems(error));
				}.bind(this));

				if(this.isAddingItem) {
					this.getProductPrice();
				}
			},

			changePriceForItem(item, price_group) {
				let prices = this.calculatePriceBasedOnCustomer(price_group);
				if(this.form.items[item].product_type_id !== 1) {
					this.form.items[item].price = prices.price;
					this.form.items[item].tax = prices.tax;
					this.form.items[item].total_price = prices.total;
				}

				if(item + 1 == this.form.items.length || this.form.items.length == 0) 
					this.isLoading = false;

			}

		},

		computed: {
			total() {
				return parseFloat(this.subtotal) + parseFloat(this.tax) - parseFloat(this.discount_value);
			},

			rounded_total() {
				return this.total + this.rounding;
			},

			rounding() {
				let rounded_total = Math.round(this.total * 100 / 5 ) / 100 * 5;
				let value = this.total - rounded_total;

				if(value !== 0)
					return -value;

				return 0.00;
			},

			total_price() {
				return this.price ? parseFloat(this.price) + parseFloat(this.item_tax) : 0.00;
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

			isParcelOrDocument() {
				return this.selectedProductType.has_detail;
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

				return value;
			},

			canEdit() {
				return this.form.items.length > 0 && ( !this.invoice ||  this.can_edit_invoice ) && ( this.selectedCustomer || this.form.paid >= this.rounded_total );
			},

			canEditItem() {
				return ( !this.invoice ||  this.can_edit_invoice ) && !this.item_add_loading;
			},

			editTooltip() {
				if(!this.canEdit)
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
				return !this.isLoading;
			}
		},

		watch: {
			selectedType(newVal, oldVal) {
				this.form.type = newVal.value;
				
				this.getPriceForItems();
			},

			zone(newVal, oldVal) {
				this.getFilteredProduct();
			},

			weight(newVal, oldVal) {
				this.getFilteredProduct();
			},

			dimension_weight(newVal, oldVal) {
				this.getFilteredProduct();
			},

			selectedCourier(newVal, oldVal) {
				if(oldVal !== newVal)
					this.getFilteredProduct();
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
				
				this.getPriceForItems();
			},

			selectedProductType(newVal, oldVal) {
				if(newVal && oldVal !== newVal)
					this.getRelatedProduct();
			}
		}	
	}
</script>