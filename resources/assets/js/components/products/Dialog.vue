<template>
	<div class="modal fade" id="sku-dialog" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">{{ title }}</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
	        		<form @submit.prevent="submit" 
						@keydown="form.errors.clear($event.target.name)" 
						@input="form.errors.clear($event.target.name)">
						<div class="row">
							<div class="col">
								<selector-input :potentialData="types"
									v-model="selectedType" 
									:defaultData="selectedType"
									placeholder="Select product type"
									:required="true"
									label="Product type"
									name="product_type_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('product_type_id')">
								</selector-input>
							</div>
							<div class="col">
								<text-input v-model="form.sku" 
									:defaultValue="form.sku"
									:required="true"
									type="text"
									label="SKU"
									name="sku"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('sku')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.description" 
									:defaultValue="form.description"
									:required="true"
									type="text"
									label="Description"
									name="description"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('description')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.weight_start" 
									:defaultValue="form.weight_start"
									:required="false"
									type="number"
									label="Weight start"
									name="weight_start"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('weight_start')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.weight_end" 
									:defaultValue="form.weight_end"
									:required="false"
									type="number"
									label="Weight end"
									name="weight_end"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('weight_end')">
								</text-input>
							</div>
						</div>	
						<div class="row">
							<div class="col">
								<text-input v-model="form.corporate_price" 
									:defaultValue="form.corporate_price"
									:required="true"
									type="number"
									label="Corporate price"
									name="corporate_price"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('corporate_price')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.walk_in_price" 
									:defaultValue="form.walk_in_price"
									:required="true"
									type="number"
									label="Walk in price"
									name="walk_in_price"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('walk_in_price')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.walk_in_price_special" 
									:defaultValue="form.walk_in_price_special"
									:required="true"
									type="number"
									label="Walk in price (special)"
									name="walk_in_price_special"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('walk_in_price_special')">
								</text-input>
							</div>
						</div>
						<div class="row mb-2">
							<div class="col">
								<selector-input :potentialData="taxes"
									v-model="selectedTax" 
									:defaultData="selectedTax"
									placeholder="Select tax type"
									:required="true"
									label="Tax"
									name="tax_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('tax_id')">
								</selector-input>
								<checkbox-input v-model="form.is_tax_inclusive"
									:defaultChecked="form.is_tax_inclusive"
									label="Tax inclusive"
									name="is_tax_inclusive"
									:editable="true">
								</checkbox-input>
							</div>
							<div class="col">
								<selector-input :potentialData="vendors"
									v-model="selectedVendor" 
									:defaultData="selectedVendor"
									placeholder="Select vendor"
									:required="false"
									label="Vendor"
									name="vendor_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('vendor_id')">
								</selector-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<selector-input :potentialData="zonetypes"
									v-model="selectedZoneType" 
									:defaultData="selectedZoneType"
									placeholder="Select zone type"
									:required="false"
									label="Zone type"
									name="zone_type_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('zone_type_id')">
								</selector-input>
							</div>
							<div class="col">
								<text-input v-model="form.zone" 
									:defaultValue="form.zone"
									:required="false"
									type="number"
									label="Zone"
									name="zone"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('zone')">
								</text-input>
							</div>
						</div>
					</form>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-primary" @click="submit" v-html="action"></button>
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>
	  	</div>
	  	<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
	</div>
</template>

<script>
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
	export default {
		props: [''],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedProduct: '',
				vendors: [],
				types: [],
				zonetypes: [],
				taxes: [],
				selectedVendor: '',
				selectedType: '',
				selectedZoneType: '',
				selectedTax: '',
				isEdit: false,
				form: new Form({
					sku: '',
					description: '',
					zone: '',
					weight_start: '',
					weight_end: '',
					is_tax_inclusive: '',
					corporate_price: '',
					walk_in_price: '',
					walk_in_price_special: '',
					vendor_id: '',
					product_type_id: '',
					tax_id: '',
					zone_type_id: ''
				})
			};
		},

		mounted() {
			window.events.$on('createProduct', evt => this.createProduct(evt));
			window.events.$on('editProduct', evt => this.editProduct(evt));

			$("#sku-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getVendor();
		},

		methods: {
			getVendor() {
				axios.get('/data/vendors')
					.then(response => this.setVendor(response))
					.catch(error => this.getVendor());
			},

			setVendor(response) {
				this.vendors = response.data.map(function(vendor){
					let obj = {};

					obj['label'] = vendor.name;
					obj['value'] = vendor.id;

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
				this.zonetypes = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;

					return obj;
				});

				this.getProductType();
			},

			getProductType() {
				axios.get("/data/producttypes")
					.then(response => this.setProductType(response))
					.catch(error => this.getProductType());
			},

			setProductType(response) {
				this.types = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;

					return obj;
				});

				this.getTaxes();
			},

			getTaxes() {
				axios.get("/data/taxes")
					.then(response => this.setTaxes(response))
					.catch(error => this.getTaxes());
			},

			setTaxes(response) {
				this.taxes = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.code;
					obj['value'] = type.id;

					return obj;
				});
			},

			createProduct(evt) {
				this.openDialog();
				
			},

			editProduct(evt) {
				this.selectedProduct = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#sku-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedProduct = '';
				this.form.reset();
				this.selectedVendor = '';
				this.selectedZoneType = '';
				this.selectedTax = '';
				this.selectedType = '';
			},

			setForm() {
				this.form.sku = this.selectedProduct.sku;
				this.form.description = this.selectedProduct.description;
				this.form.zone = this.selectedProduct.zone;
				this.form.weight_start = this.selectedProduct.weight_start;
				this.form.weight_end = this.selectedProduct.weight_end;
				this.form.is_tax_inclusive = this.selectedProduct.is_tax_inclusive;
				this.form.corporate_price = this.selectedProduct.corporate_price + "";
				this.form.walk_in_price = this.selectedProduct.walk_in_price + "";
				this.form.walk_in_price_special = this.selectedProduct.walk_in_price_special + "";
				this.form.vendor_id = this.selectedProduct.vendor_id;
				this.form.product_type_id = this.selectedProduct.product_type_id;
				this.form.tax_id = this.selectedProduct.tax_id;
				this.form.zone_type_id = this.selectedProduct.zone_type_id;

				this.selectedVendor = '';
				this.selectedTax = '';
				this.selectedType = '';
				this.selectedZoneType = '';

				if(this.form.zone_type_id) {
					this.selectedZoneType = _.filter(this.zonetypes, function(type){ return this.form.zone_type_id == type.value; }.bind(this))[0];
				}

				if(this.form.vendor_id) {
					this.selectedVendor = _.filter(this.vendors, function(type){ return this.form.vendor_id == type.value; }.bind(this))[0];
				}

				if(this.form.tax_id) {
					this.selectedTax = _.filter(this.taxes, function(type){ return this.form.tax_id == type.value; }.bind(this))[0];
				}

				if(this.form.product_type_id) {
					this.selectedType = _.filter(this.types, function(type){ return this.form.product_type_id == type.value; }.bind(this))[0];
				}

			},

			submit() {
				this.isConfirming = true;
			},

			confirmSubmit() {
				this.isConfirming = false;
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#sku-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedProduct ? "Edit SKU - " + this.selectedProduct.sku : "Create SKU";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedProduct ? "Update" : "Create";
			},

			url() {
				return this.selectedProduct ? "/admin/products/" + this.selectedProduct.id : "/admin/products";
			}
		},

		watch: {
			selectedZoneType(newVal, oldVal) {
				this.form.zone_type_id = newVal.value;
			},

			selectedVendor(newVal, oldVal) {
				this.form.vendor_id = newVal.value;
			},

			selectedType(newVal, oldVal) {
				this.form.product_type_id = newVal.value;
			},

			selectedTax(newVal, oldVal) {
				this.form.tax_id = newVal.value;
			}
		}


	}
</script>