<template>
	<div class="invoice-items">
		<span>{{ index + 1 }}</span>
		<div class="small-select">
			<selector-input :potentialData="product_types"
				v-model="selectedProductType" 
				:defaultData="selectedProductType"
				placeholder="Select product type"
				:required="true"
				label="Product type"
				name="product_type"
				:editable="canEdit"
				:focus="false"
				:hideLabel="true"
				:error="selectedProductType_error"
				:unclearable="true"
				ref="producttypes"
				@input="getProducts">
			</selector-input>
		</div>
		<div class="small-select">
			<selector-input :potentialData="zone_types"
				v-model="selectedZoneType" 
				:defaultData="selectedZoneType"
				placeholder="Select zone type"
				:required="false"
				label="Zone type"
				name="zone_type"
				:editable="canEdit"
				:focus="false"
				:hideLabel="true"
				:unclearable="true"
				:error="selectedZoneType_error"
				@input="getDefaultDetails">
			</selector-input>
		</div>
		<text-input v-model="zone" 
			:defaultValue="zone"
			:required="true"
			type="number"
			label="Zone"
			name="zone"
			:editable="true"
			:focus="false"
			:hideLabel="true"
			:error="zone_error"
			step="1"
			ref="zone"
			:disabled="!has_detail || !canEdit"
			@input="updateProducts">
		</text-input>
			
		<text-input v-model="weight" 
			:defaultValue="weight"
			:required="true"
			type="number"
			label="Weight (KG)"
			name="weight"
			:editable="true"
			:focus="false"
			:hideLabel="true"
			:error="weight_error"
			:disabled="!has_detail || !canEdit"
			@input="updateProducts">
		</text-input>
			
		<text-input v-model="dimension_weight" 
			:defaultValue="dimension_weight"
			:required="false"
			type="number"
			label="Dimension weight (KG)"
			name="dimension_weight"
			:editable="true"
			:focus="false"
			:hideLabel="true"
			ref="dimension"
			:error="dimension_weight_error"
			@input="updateProducts"
			@enter="openDimWeightModal"
			:disabled="!has_detail || !canEdit"
			@dblclick="openDimWeightModal">
		</text-input>

		<div class="small-select">
			<selector-input :potentialData="couriers"
				v-model="selectedCourier" 
				:defaultData="selectedCourier"
				placeholder="Select courier"
				:required="false"
				label="Courier"
				name="courier"
				:editable="canEdit"
				:focus="false"
				:hideLabel="true"
				:unclearable="true"
				:error="selectedCourier_error"
				:disabled="!has_detail && canEdit"
				@input="updateProducts">
			</selector-input>
		</div>
		<div class="small-select">
			<selector-input :potentialData="products"
				v-model="selectedProduct" 
				:defaultData="selectedProduct"
				placeholder="Select SKU"
				:required="true"
				label="SKU"
				name="product"
				:editable="canEdit"
				:focus="false"
				:hideLabel="true"
				:error="selectedProduct_error"
				:unclearable="true"
				@input="productChange">
			</selector-input>
		</div>

		<text-input v-model="description" 
			:defaultValue="description"
			:required="true"
			type="text"
			label="Description"
			name="description"
			:editable="true"
			:focus="false"
			:hideLabel="true"
			:disabled="!canEdit"
			:error="description_error">
		</text-input>

		<text-input v-model="unit" 
			:defaultValue="unit"
			:required="true"
			type="number"
			label="Unit"
			name="unit"
			:editable="true"
			:disabled="!has_detail || !canEdit"
			:focus="false"
			:hideLabel="true"
			:error="unit_error"
			@tab="massInput">
		</text-input>

		<text-input v-model="tracking_no" 
			:defaultValue="tracking_no"
			:required="this.selectedProductType.has_detail"
			type="text"
			label="Tracking no"
			name="tracking_no"
			:editable="true"
			:focus="false"
			:hideLabel="true"
			:error="tracking_no_error"
			ref="tracking_input"
			:disabled="!canEdit"
			@tab="$emit('addItem')">
		</text-input>
			
		<text-input v-model="price" 
			:defaultValue="price"
			:required="true"
			type="number"
			label="Price"
			name="price"
			:editable="true"
			:focus="false"
			:hideLabel="true"
			:error="price_error"
			:disabled="!canEdit"
			@input="is_custom_pricing = true"
			@tab="$emit('addItem')">
		</text-input>
		
		<text-input v-model="total_price" 
			:defaultValue="total_price"
			:required="true"
			type="number"
			label="Total price"
			name="total_price"
			:editable="false"
			:focus="false"
			:hideLabel="true">
		</text-input>

		<div><button type="button" class="btn btn-sm btn-danger" @click="$emit('delete')" :disabled="!canEdit">Delete</button></div>

		<modal :active="isCalculatingDimWeight"
			:id="'dim-weight-calculator-' + this.index"
			@close="closeDimWeight">

			<span slot="header">Calculate dimension weight</span>

			<p v-if="selectedCourier"> Selected courier: {{ selectedCourier.label }} - {{ selectedCourier.formula }}</p>
			<p class="text-danger" v-else>Please select courier first</p>
			<text-input v-model="height" 
				:defaultValue="height"
				:required="true"
				type="number"
				label="Height (cm)"
				name="height"
				ref="heightinput"
				:editable="true"
				:focus="false"
				:hideLabel="false">
			</text-input>
			<text-input v-model="width" 
				:defaultValue="width"
				:required="true"
				type="number"
				label="Width (cm)"
				name="width"
				:editable="true"
				:focus="false"
				:hideLabel="false">
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
				@enter="calculateDimWeight">
			</text-input>

			<template slot="footer">
				<button type="button" class="btn btn-secondary" @click="closeDimWeight">Cancel</button>
				<button type="button" class="btn btn-primary" @click="calculateDimWeight" :disabled="!selectedCourier">Confirm</button>
			</template>
		</modal>
	</div>
</template>

<script>
	export default {
		props: ['items', 'item', 'canEdit', 'index', 'product_types', 'zone_types', 'couriers', 'defaultProductType', 'selectedType', 'selectedCustomer'],

		data() {
			return {
				isCalculatingDimWeight: false,
				tracking_no: '',
				selectedProductType: this.defaultProductType,
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
				// Based on entered price
				// item_tax: 0,
				tax_rate: 0,
				is_custom_pricing: false,
				item_tax_inclusive: '',
				has_detail: true,
				tax_type: '',

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

				tracking_no_repeating: '',

				products: []
			};
		},

		mounted() {
			window.events.$on('update_price', function() {
				Vue.nextTick(function() {
					this.getProductPrice();
				}.bind(this));
			}.bind(this));

			window.events.$on("updateItemsValue", function() {
				this.updateItem();
			}.bind(this));

			// If we are getting item from db
			Vue.nextTick( () => {
				console.log(this.item.product_id);
				if(this.item.product_id) {
					this.updateItem();
				}
				else if(this.defaultProductType.has_detail) {
					this.selectedZoneType = {label: 'Domestic', value: 1};
					Vue.nextTick( function() { this.getDefaultDetails(); }.bind(this));
				}

			});
			
			if(this.item.shouldFocus)
				this.$refs.producttypes.focus();
		},

		methods: {
			updateItem(){
				// console.log("Updating item!" + this.item.description);
				this.tracking_no = this.item.tracking_code;
				
				this.zone = this.item.zone;
				this.weight = this.item.weight;
				this.dimension_weight = this.item.dimension_weight;

				this.description = this.item.description;
				this.price = this.item.price;
				this.unit = this.item.unit;
				this.width = this.item.width;
				this.length = this.item.length;
				this.height = this.item.height;

				this.tax_rate = this.item.tax_rate;
				this.is_custom_pricing = this.item.is_custom_pricing;
				this.item_tax_inclusive = this.item.item_tax_inclusive;

				this.selectedProductType = _.filter(this.product_types, function(type){ return this.item.product_type_id == type.value; }.bind(this))[0];
				this.selectedZoneType = this.zone_types[0];
				this.selectedCourier = _.filter(this.couriers, function(courier){ return this.item.courier_id == courier.value; }.bind(this))[0];
				
				this.has_detail = this.selectedProductType.has_detail;

				this.getProducts();
			},

			openDimWeightModal() {
				// console.log("Opening dim weight");
				this.isCalculatingDimWeight = true;
				if(!this.dimension_weight) {
					this.height = "";
					this.width = "";
					this.length = "";
				}
				setTimeout(function(){ this.$refs.heightinput.triggerFocus() }.bind(this), 500 );
			},

			closeDimWeight() {
				// console.log("Closing dim weight");
				if(!this.dimension_weight) {
					this.height = 0;
					this.width = 0;
					this.length = 0;
				}

				this.isCalculatingDimWeight = false;
			},

			calculateDimWeight(shouldFocus = true) {
				let formula = this.selectedCourier.formula;

				let expression = formula.replace("l", this.length);
				expression = expression.replace("w", this.width);
				expression = expression.replace("h", this.height);
				if(this.height > 0) {
					this.dimension_weight = eval(expression);
					this.closeDimWeight();
					this.updateProducts();
					if(shouldFocus)
						this.$refs.dimension.triggerFocus();
				}
			},

			getProducts(error = 'No error') {
				// console.log("Getting product");
				// console.log(this.selectedProductType);
				// Product type selected, get the products of the same type
				if(this.selectedProductType) {
					this.has_detail = this.selectedProductType.has_detail;
					axios.get('/data/products?type=' + this.selectedProductType.value)
						.then(response => this.setProducts(response))
						.catch(error => this.getRelatedProduct(error));
				}
			},

			setProducts(response) {
				this.selectedProduct = '';
				this.selectedProduct_error = "";
				this.products = response.data.map(function(product){
					let obj = {};

					obj['value'] = product.id;
					obj['label'] = product.sku;
					obj['description'] = product.description;
					obj['is_tax_inclusive'] = product.is_tax_inclusive;


					return obj;
				}.bind(this));

				// If we already have item
				if(this.item.product_type_id) {
					this.selectedProduct = _.filter(this.products, function(type){ return this.item.product_id == type.value; }.bind(this))[0];
				}

				// If we only have 1 product, set it as default
				if(this.products.length == 1 && !this.selectedProduct) {
					this.selectedProduct = this.products[0];
				}
				// If we dont have any products that matches
				if(this.products.length == 0) {
					this.selectedProduct_error = "No matching SKU found";
				}

				// If selected product types doesn't have details, clear the courier field and disable zone/weight/dim weight fields
				if(!this.selectedProductType.has_detail) {
					this.selectedCourier = '';
				}
			},

			updateProducts(error = "No error") {
				if(this.selectedProductType.has_detail && this.zone && (this.weight || this.dimension_weight) && this.selectedCourier){
					let url = "/data/products?type=" + this.selectedProductType.value + "&zone=" + this.zone;

					if(this.weight)
						url += "&weight=" + this.weight;

					if(this.dimension_weight)
						url += "&dimension=" + this.dimension_weight;

					if(this.selectedCourier)
						url += "&vendor=" + this.selectedCourier.value;

					axios.get(url)
						.then(response => this.setProducts(response))
						.catch(error => this.updateProducts(error));
				}
			},

			productChange() {
				if(this.selectedProduct) {
					this.description = this.selectedProduct.description;
					this.item_tax_inclusive = this.selectedProduct.is_tax_inclusive;
					this.getProductPrice();
				}
			},

			getDefaultDetails(error = 'No error') {
				if(!this.item.product_id) {
					axios.get("/data/branch/knowledge?type=" + this.selectedProductType.label)
						.then(response => this.setDefaultDetails(response))
						.catch(error => this.getDefaultDetails(error));
				}
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

			/* Methods to get product price */

			getProductPriceUrl(product) {

				let url = "/data/pricing?product=" + product;
				if(this.selectedType.label == "Customer" && this.selectedCustomer) {
					url += "&customer=" + this.selectedCustomer.value;
				}

				return url;
			},

			getProductPrice(error = 'No error') {
				// console.log(error);
				if(this.selectedProduct) {
					this.item_add_loading = true;

					let url = this.getProductPriceUrl(this.selectedProduct.value);
					
					// console.log(url);

					axios.get(url)
						.then(response => this.setProductPrice(response))
						.catch(error => this.getProductPrice(error));
				}

				this.setProductPrice('');
			},

			setProductPrice(response) {
				// console.log("Setting product price");
				if(response) {
					this.price_group = this.selectedProduct;

					if(response.data)
						this.price_group = response.data;

					let prices = this.calculatePriceBasedOnCustomer(this.price_group)
					this.price = Math.round(prices.price * 100) / 100;
					// Based on entered price
					// this.item_tax = prices.tax;
					this.tax_rate = prices.tax_rate;
					this.item_tax_inclusive = prices.is_tax_inclusive;
					this.tax_type = prices.tax_type;
				}

				this.item_add_loading = false;
			},

			calculatePriceBasedOnCustomer(price_group) {
				let price = price_group.walk_in_price;
				let tax_rate = price_group.tax / 100;

				if(this.selectedType.label == "Customer" && this.selectedCustomer)
				{
					if(this.selectedCustomer.type == 'walk_in_special')
						price = price_group.walk_in_price_special;
					else if(this.selectedCustomer.type == 'Corporate')
						price = price_group.corporate_price;
				}

				let tax = price_group.tax ? price * tax_rate: 0;
				let total = price + tax;

				return {price: price, tax: tax, tax_rate: tax_rate, total: total, is_tax_inclusive: price_group.is_tax_inclusive, tax_type: price_group.code};
			},

			massInput() {
				if(parseInt(this.unit) > 1)
					this.$emit("mass");
				else
					this.$refs.tracking_input.triggerFocus();
			},

			checkTrackingNo: _.debounce(function (error = "No error") {
				console.log(error);
				axios.get("/data/trackings/check?code=" + this.tracking_no)
					.then(response => this.setTrackingNoResult(response))
					.catch(error => this.checkTrackingNo(error));
			}, 1000),

			setTrackingNoResult(response) {
				// console.log(response.data.result);
				this.tracking_no_repeating = response.data.result;

				if(!response.data.result) {
					this.tracking_no_repeating = _.filter(this.items, function(item){
						return item.tracking_code && item.tracking_code == this.tracking_no;
					}.bind(this)).length > 1;
				}
			}
		},

		computed: {
			item_tax() {
				let tax = this.item_tax_inclusive ? this.price - ( Math.round(this.price / ( this.tax_rate + 1) * 100 ) / 100 ) : Math.round(this.price * this.tax_rate * 100) / 100;

				return tax.toFixed(2);
			},

			total_price() {
				let price = 0;
				if(this.price)
					price = this.item_tax_inclusive ? parseFloat(this.price) : parseFloat(this.price) + parseFloat(this.item_tax) ;

				return price.toFixed(2);
			},

			tracking_no_error() {
				if(this.selectedProductType.has_detail && this.description && !this.tracking_no)
					// We already have a product which needs tracking code selected but tracking code not entered
					return 'Tracking code is required';
				else if(this.tracking_no_repeating)
					return 'Invalid tracking code';

				return '';
			}
		},

		watch: {
			tracking_no(newVal) {
				this.checkTrackingNo();
				this.$emit('update', {attribute: 'tracking_code', value: newVal});
			},

			tracking_no_error(newVal) {
				this.$emit('update', {attribute: 'has_error', value: newVal != ''});
			},

			selectedProductType(newVal) {
				this.$emit('update', {attribute: 'product_type_id', value: ''});
				if(newVal) {
					this.$emit('update', {attribute: 'product_type_id', value: newVal.value});
					this.getDefaultDetails();
				}
			},

			selectedZoneType(newVal) {
				this.$emit('update', {attribute: 'zone_type_id', value: ''});
				if(newVal) {
					this.$emit('update', {attribute: 'zone_type_id', value: newVal.value});
				}

			},

			zone(newVal) {
				this.$emit('update', {attribute: 'zone', value: newVal});
			},

			weight(newVal) {
				this.$emit('update', {attribute: 'weight', value: newVal ? newVal : 0});
			},

			dimension_weight(newVal) {
				this.$emit('update', {attribute: 'dimension_weight', value: newVal ? newVal : 0});
			},

			height(newVal) {
				this.$emit('update', {attribute: 'height', value: newVal ? newVal : 0});
			},

			length(newVal) {
				this.$emit('update', {attribute: 'length', value: newVal ? newVal : 0});
			},

			width(newVal) {
				this.$emit('update', {attribute: 'width', value: newVal ? newVal : 0});
			},

			selectedCourier(newVal) {
				this.$emit('update', {attribute: 'courier_id', value: 0});
				if(newVal) {
					this.$emit('update', {attribute: 'courier_id', value: newVal.value});	

					// console.log("Updated courier id: " + newVal.value);			
					this.calculateDimWeight(false);
				}
			},

			price(newVal) {
				this.$emit('update', {attribute: 'price', value: newVal});
			},

			description(newVal) {
				this.$emit('update', {attribute: 'description', value: newVal});
			},

			selectedProduct(newVal) {
				this.$emit('update', {attribute: 'product_id', value: ''});
				this.$emit('update', {attribute: 'sku', value: ''});
				if(newVal) {
					this.$emit('update', {attribute: 'product_id', value: newVal.value});
					this.$emit('update', {attribute: 'sku', value: newVal.label});
				}
			},

			total_price(newVal) {
				this.$emit('update', {attribute: 'total_price', value: newVal});
			},

			item_tax(newVal) {
				this.$emit('update', {attribute: 'tax', value: newVal});
			},

			item_tax_inclusive(newVal) {
				this.$emit('update', {attribute: 'item_tax_inclusive', value: newVal});
			},

			is_custom_pricing(newVal) {
				this.$emit('update', {attribute: 'is_custom_pricing', value: newVal ? newVal : 0});
			},

			tax_rate(newVal) {
				this.$emit('update', {attribute: 'tax_rate', value: newVal ? newVal : 0});
			},

			tax_type(newVal) {
				this.$emit('update', {attribute: 'tax_type', value: newVal ? newVal : 'SR'});
			},
		}	
	}
</script>