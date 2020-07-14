<template>
	<div class="modal fade" id="inventory-product-dialog" tabindex="-1" role="dialog">
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
								<selector-input :potentialData="inventories"
									v-model="selectedInventory" 
									:defaultData="selectedInventory"
									placeholder="Select inventory"
									:required="true"
									label="Inventory"
									name="inventory_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('inventory_id')">
								</selector-input>
							</div>							
						</div>	
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
						</div>	
                        <div class="row">
                            <div class="col">
								<text-input v-model="form.quantity" 
									:defaultValue="form.quantity"
									:required="true"
									type="number"
									label="Quantity"
									name="quantity"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('quantity')">
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
                selectedInventoryProduct: '',
                inventories: [],
                products: [],
                selectedInventory: '',
                selectedProduct: '',
				isEdit: false,
				isDelete: false,
				form: new Form({
                    inventory_id: '',
                    product_id: '',
					quantity: '',					
				})
			};
		},

		mounted() {
			window.events.$on('createInventoryProduct', evt => this.createInventoryProduct(evt));
			window.events.$on('editInventoryProduct', evt => this.editInventoryProduct(evt));
			window.events.$on('deleteInventoryProduct', evt => this.deleteInventoryProduct(evt));

			$("#inventory-product-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getInventory();
		},

		methods: {	
            getInventory() {
				axios.get("/data/inventories")
					.then(response => this.setInventory(response))
					.catch(error => this.getInventory());
			},

			setInventory(response) {
				this.inventories = response.data.map(function(inventory){
					let obj = {};

					obj['label'] = inventory.name;
					obj['value'] = inventory.id;

					return obj;
                });
                
                this.getProduct();
			},
	
			getProduct() {
				axios.get("/data/products")
					.then(response => this.setProduct(response))
					.catch(error => this.getProduct());
			},

			setProduct(response) {
				this.products = response.data.map(function(product){
					let obj = {};

					obj['label'] = product.sku;
					obj['value'] = product.id;

					return obj;
				});
			},

			createInventoryProduct(evt) {
				this.openDialog();				
			},

			editInventoryProduct(evt) {
				this.selectedInventoryProduct = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#inventory-product-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
                this.isActive = false;
                this.selectedInventoryProduct = '';
                this.selectedInventory = '';
                this.selectedProduct = '';
				this.form.reset();
				this.isEdit = false;
			},

			setForm() {
				this.form.quantity = this.selectedInventoryProduct.quantity;				
				this.form.product_id = this.selectedInventoryProduct.product_id;
                this.form.inventory_id = this.selectedInventoryProduct.inventory_id;

				this.selectedProduct = '';
				if(this.form.product_id) {
					this.selectedProduct = _.filter(this.products, function(product){ return this.form.product_id == product.value; }.bind(this))[0];
                }
                this.selectedInventory = '';
				if(this.form.inventory_id) {
					this.selectedInventory = _.filter(this.inventories, function(inventory){ return this.form.inventory_id == inventory.value; }.bind(this))[0];
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
				$("#inventory-product-dialog").modal('hide');
				this.closeDialog();
				window.events.$emit("reload-table");
			},

			deleteInventoryProduct(evt) {
				this.selectedInventoryProduct= evt[0];
				this.isDelete = true;
				this.isConfirming = true;
			},
		},

		computed: {
			title() {
				return this.selectedInventoryProduct ? "Edit Inventory Product" : "Create Inventory Product";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedInventoryProduct ? "Update" : "Create";
			},

			url() {
				if(!this.isDelete) {
					return this.selectedInventoryProduct ? "/admin/inventory_product/" + this.selectedInventoryProduct.id : "/admin/inventory_product";
				}
				else {
					return "/admin/inventory_product/" + this.selectedInventoryProduct.id + "/delete";
				}					
			}
		},

		watch: {
			selectedInventory(newVal, oldVal) {
				this.form.inventory_id = newVal.value;
            },
            selectedProduct(newVal, oldVal) {
				this.form.product_id = newVal.value;
			},
		}
	}
</script>