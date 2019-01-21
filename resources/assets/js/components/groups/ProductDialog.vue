<template>
	<div class="modal fade" id="customer-group-product-dialog" tabindex="-1" role="dialog">
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
								<selector-input :potentialData="products"
									v-model="selectedProduct" 
									:defaultData="selectedProduct"
									placeholder="Select product"
									:required="true"
									label="Product"
									name="product_id"
									:editable="!isEdit"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('product_id')"
									ref="products">
								</selector-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="selectedProduct.zone" 
									:defaultValue="selectedProduct.zone"
									:required="true"
									type="text"
									label="Zone"
									name="zone"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('zone')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="selectedProduct.courier" 
									:defaultValue="selectedProduct.courier"
									:required="true"
									type="text"
									label="Vendor"
									name="courier"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('courier')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="selectedProduct.type" 
									:defaultValue="selectedProduct.type"
									:required="true"
									type="text"
									label="Type"
									name="type"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('type')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="selectedProduct.weights" 
									:defaultValue="selectedProduct.weights"
									:required="true"
									type="text"
									label="Weight range"
									name="weights"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('weights')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.walk_in_price" 
									:defaultValue="form.walk_in_price"
									:required="true"
									type="text"
									label="Walk in price"
									name="walk_in_price"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('walk_in_price')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.walk_in_price_special" 
									:defaultValue="form.walk_in_price_special"
									:required="true"
									type="text"
									label="Walk in price special"
									name="walk_in_price_special"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('walk_in_price_special')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.corporate_price" 
									:defaultValue="form.corporate_price"
									:required="true"
									type="text"
									label="Corporate price"
									name="corporate_price"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('corporate_price')">
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
		props: ['user', 'group'],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedProduct: '',
				originalProduct: '',
				products: [],
				isEdit: false,
				form: new Form({
					customer_group_id: '',
					product_id: '',
					walk_in_price_special: '',
					walk_in_price: '',
					corporate_price: '',
					group_id: this.group.id,
				}),

			};
		},

		mounted() {

			window.events.$on('createCustomerGroupProduct', evt => this.createCustomerGroupProduct(evt));
			window.events.$on('editCustomerGroupProduct', evt => this.editCustomerGroupProduct(evt));
			window.events.$on('deletedCustomerGroupProduct', evt => this.deleteCustomerGroupProduct(evt));

			this.getProducts();

			$("#customer-group-product-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
			getProducts(error = "No error", retry = 3){
				if(retry <= 3) {
					// console.log(error.label);
					axios.get('/data/products?full=1')
						.then(response => this.setProducts(response))
						.catch(error => this.getProducts(error, ++retry));
				}
			},

			setProducts(response) {
				if(response.data) {
					this.products = response.data.map(function(product){
						let obj = {};

						obj['value'] = product.id;
						obj['label'] = product.sku + " | " + product.description;
						obj['walk_in_price'] = product.walk_in_price;
						obj['walk_in_price_special'] = product.walk_in_price_special;
						obj['corporate_price'] = product.corporate_price;
						obj['zone'] = product.zone ? product.zone : "-";
						obj['courier'] = product.vendor ? product.vendor.name : "-";
						obj['type'] = product.product_type.name;
						obj['weights'] = product.weight_start ? product.weight_start + " - " + product.weight_end : "-";

						return obj;
					});

					if(this.isEdit) this.setSelectedProduct();
				}
			},

			createCustomerGroupProduct(evt) {
				this.openDialog();	
			},

			editCustomerGroupProduct(evt) {
				this.isEdit = true;
				this.originalProduct = evt[0];
				this.setForm();
				this.openDialog();
			},

			deleteCustomerGroupProduct(evt) {
				flash(evt.data.message);
				window.events.$emit("reload-table");
			},

			openDialog() {
				$("#customer-group-product-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				// console.log("Closing dialog");
				this.isActive = false;
				this.originalProduct = '';
				this.form.walk_in_price_special = '';
				this.form.walk_in_price = '';
				this.form.corporate_price = '';
				this.form.product_id = '';
				this.selectedProduct = '';

				this.isEdit = false;
				this.form.reset();
			},

			setForm() {
				this.form.product_id = this.originalProduct.product_id;

				this.setSelectedProduct();		

				this.form.walk_in_price_special = this.originalProduct.walk_in_price_special > 0 ? this.originalProduct.walk_in_price_special : "0.00";
				this.form.walk_in_price = this.originalProduct.walk_in_price > 0 ? this.originalProduct.walk_in_price : "0.00";
				this.form.corporate_price = this.originalProduct.corporate_price > 0 ? this.originalProduct.corporate_price : "0.00";
				

						
			},

			setSelectedProduct() {
				this.selectedProduct = _.filter(this.products, function(product){ return product.value == this.originalProduct.id; }.bind(this))[0];
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
				$("#customer-group-product-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}

		},

		computed: {
			title() {
				return this.isEdit ? "Edit customer group product: " + this.group.name + " | " + this.originalProduct.sku : "Create customer group product";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.isEdit ? "Update" : "Create";
			},

			url() {
				return this.isEdit ? "/groups/" + this.group.id + "/products/" + this.originalProduct.id : "/groups/" + this.group.id + "/products";
			}
		},

		watch: {
			selectedProduct(val) {
				// console.log("Clearing customers");
				if(val) {
					this.form.product_id = val.value;
					if(!this.isEdit) {
						this.form.walk_in_price = val.walk_in_price;
						this.form.walk_in_price_special = val.walk_in_price_special;
						this.form.corporate_price = val.corporate_price;
					}
				} else {
					this.form.product_id = '';
				}
			}
		}
	}
</script>