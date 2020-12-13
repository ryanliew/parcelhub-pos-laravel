<template>
	<div class="modal fade" id="stock-dialog" tabindex="-1" role="dialog">
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
						</div>
						 <div class="row">
                            <div class="col">
								<text-input v-model="form.date" 
									:defaultValue="form.date"
									:required="true"
									type="date"
									label="Date"
									name="date"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('date')">
								</text-input>
							</div>                            
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.invoice_no" 
									:defaultValue="form.invoice_no"
									:required="false"
									type="text"
									label="Invoice no"
									name="invoice_no"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('invoice_no')">
								</text-input>
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
                        <div class="row">
							<div class="col">
								<checkbox-input v-model="form.active"
									:defaultChecked="form.active"
									label="Active"
									name="active"
									:editable="true">
								</checkbox-input>
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
                selectedStock: '',                
                inventories: [],
                types:['In', 'Out'],
                selectedInventory: '',
                selectedType: '',
				isEdit: false,
				isDelete: false,
				form: new Form({
                    inventory_id: '',
                    quantity: 0,
                    type: '',
                    date: '',
                    invoice_no: '',
					active: 1,					
				})
			};
		},

		mounted() {
			window.events.$on('createStock', evt => this.createStock(evt));
			window.events.$on('editStock', evt => this.editStock(evt));
			window.events.$on('deleteStock', evt => this.deleteStock(evt));

			$("#stock-dialog").on("hide.bs.modal", function(e){
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
			},

			createStock(evt) {
				this.openDialog();				
			},

			editStock(evt) {
				this.selectedStock = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#stock-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
                this.selectedStock = '';
                this.selectedInventory = '';
                this.selectedType = '';
				this.form.reset();
				this.isEdit = false;
			},

			setForm() {
				this.form.inventory_id = this.selectedStock.inventory_id;				
                this.form.quantity = this.selectedStock.quantity;
                this.form.type = this.selectedStock.type;
                this.form.date = moment(String(this.selectedStock.date)).format('YYYY-MM-DD');
                this.form.invoice_no = this.selectedStock.invoice_no;
                this.form.active = this.selectedStock.active;

				this.selectedInventory = '';
				if(this.form.inventory_id) {
					this.selectedInventory = _.filter(this.inventories, function(inventory){ return this.form.inventory_id == inventory.value; }.bind(this))[0];
                }
                
                this.selectedType = this.selectedStock.type;
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
				$("#stock-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			},

			deleteStock(evt) {
				this.selectedStock= evt[0];
				this.isDelete = true;
				this.isConfirming = true;
			},
		},

		computed: {
			title() {
				return this.selectedStock ? "Edit Stock" : "Create Stock";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedStock ? "Update" : "Create";
			},

			url() {	
				if(!this.isDelete) {
					return this.selectedStock ? "/admin/stocks/" + this.selectedStock.id : "/admin/stocks";
				}
				else {
					return "/admin/stocks/" + this.selectedStock.id + "/delete";
				}
			}
		},

		watch: {
			selectedInventory(newVal, oldVal) {
				this.form.inventory_id = newVal.value;
			},
			selectedType(newVal, oldVal) {
				this.form.type = newVal;
			},
		}
	}
</script>