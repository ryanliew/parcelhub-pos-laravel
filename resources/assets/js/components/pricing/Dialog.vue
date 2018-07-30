<template>
	<div class="modal fade" id="branch-product-dialog" tabindex="-1" role="dialog">
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
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('product_id')">
								</selector-input>
							</div>
							<div class="col">
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
									:error="form.errors.get('customer_id')">
								</selector-input>
							</div>
						</div>
						<div class="row" v-if="selectedProduct && selectedCustomer">
							<div class="col">
								<text-input v-model="form.corporate_override" 
									:defaultValue="form.corporate_override"
									:required="true"
									type="number"
									label="Corporate price"
									name="corporate_override"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('corporate_override')"
									:helpText="'Original: RM' + selectedProduct.corporate_price.toFixed(2)">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.walk_in_override" 
									:defaultValue="form.walk_in_override"
									:required="true"
									type="number"
									label="Walk in price"
									name="walk_in_override"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('walk_in_override')"
									:helpText="'Original: RM' + selectedProduct.walk_in_price.toFixed(2)">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.walk_in_special_override" 
									:defaultValue="form.walk_in_special_override"
									:required="true"
									type="number"
									label="Walk in price (special)"
									name="walk_in_special_override"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('walk_in_special_override')"
									:helpText="'Original: RM' + selectedProduct.walk_in_price_special.toFixed(2)">
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
	</div>
</template>

<script>
	export default {
		props: ['data'],
		data() {
			return {
				isActive: false,
				products: [],
				customers: [],
				selectedProduct: '',
				selectedCustomer: '',
				isEdit: false,
				form: new Form({
					product_id: '',
					customer_id: '',
					corporate_override: '',
					walk_in_override: '',
					walk_in_special_override: ''
				})
			};
		},

		mounted() {
			window.events.$on('createBranchProduct', evt => this.createBranchProduct(evt));
			window.events.$on('editBranchProduct', evt => this.editBranchProduct(evt));

			$("#branch-product-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getCustomers();
		},

		methods: {
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

					return obj;
				});

				this.getProducts();
			},

			getProducts(error = 'No error') {
				axios.get("/data/products")
					.then(response => this.setProducts(response))
					.catch(error => this.getProducts(error));
			},

			setProducts(response) {
				this.products = response.data.map(function(product){
					let obj = {};

					obj['value'] = product.id;
					obj['label'] = product.sku;
					obj['walk_in_price'] = product.walk_in_price;
					obj['walk_in_price_special'] = product.walk_in_price_special;
					obj['corporate_price'] = product.corporate_price;

					return obj;
				});
			},

			createBranchProduct(evt) {
				this.openDialog();
				
			},

			editBranchProduct(evt) {
				this.selectedProduct = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#branch-product-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedProduct = '';
				this.form.reset();
			},

			setForm() {
				this.form.product_id = this.selectedProduct.product_id;
				this.form.customer_id = this.selectedProduct.pivot.customer_id;
				this.form.corporate_price = this.selectedProduct.pivot.corporate_price;
				this.form.walk_in_price = this.selectedProduct.pivot.walk_in_price;
				this.form.walk_in_price_special = this.selectedProduct.pivot.walk_in_price_special;

				this.selectedProduct = '';
				this.selectedCustomer = '';

				if(this.form.product_id) {
					this.selectedProduct = _.filter(this.products, function(type){ return this.form.product_id == type.value; }.bind(this))[0];
				}

				if(this.form.customer_id) {
					this.selectedCustomer = _.filter(this.customers, function(type){ return this.form.customer_id == type.value; }.bind(this))[0];
				}

			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#branch-product-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.isEdit ? "Edit SKU branch- " + this.selectedProduct.sku : "Create SKU branch";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.isEdit ? "Update" : "Create";
			},

			url() {
				return this.isEdit ? "/branch/product/" + this.selectedProduct.id : "/branch/product";
			}
		},

		watch: {
			selectedProduct(newVal, oldVal) {
				this.form.product_id = newVal.value;
			},

			selectedCustomer(newVal, oldVal) {
				this.form.customer_id = newVal.value;
			}
		}


	}
</script>