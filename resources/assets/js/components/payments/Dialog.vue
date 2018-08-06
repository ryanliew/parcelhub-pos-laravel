<template>
	<div class="modal fade" id="payment-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.total" 
									:defaultValue="form.total"
									:required="true"
									type="text"
									label="Total"
									name="total"
									style="font-size: 20px"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('total')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.paid_amount" 
									:defaultValue="form.paid_amount"
									:required="true"
									type="text"
									label="Paid amount"
									name="paid_amount"
									style="font-size: 20px"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('paid_amount')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.outstanding" 
									:defaultValue="form.outstanding"
									:required="false"
									type="number"
									label="Outstanding"
									name="outstanding"
									style="font-size: 20px"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('outstanding')">
								</text-input>
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
							<div class="col">
								<text-input v-model="form.amount" 
									:defaultValue="form.amount"
									:required="true"
									type="number"
									label="Amount"
									name="amount"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('amount')">
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
		props: {
		    data: {
		        type: Object
		    }
		 },

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedInvoice: '',
				selectedPayment: '',
				isEdit: false,
				form: new Form({
					invoice_id: '',
					invoice_no: '',
					total: '',
					paid_amount: '',
					remaining: '',
					outstanding: '',
					amount: '',
					method: '',
					type: '',

				}),
				types: [
					{label: 'Cash', value: 'Cash'},
					{label: 'Cheque', value: 'Cheque'},
					{label: 'Credit card', value: 'Credit card'},
					{label: 'IBG', value: 'IBG'},
				],
				selectedType: '',
			};
		},

		mounted() {
			window.events.$on('createPayment', evt => this.createPayment(evt));
			window.events.$on('editPayment', evt => this.editPayment(evt));

			$("#payment-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
			createPayment(evt) {
				this.selectedInvoice = evt[0];
				this.setNewForm();
				this.openDialog();
				
			},

			editPayment(evt) {
				this.selectedPayment = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#payment-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedPayment = '';
				this.form.reset();
			},

			setNewForm() {
				this.form.invoice_id	= this.selectedInvoice.id;
				this.form.invoice_no 	= this.selectedInvoice.invoice_no; 
				this.form.total 		= this.selectedInvoice.total;
				this.form.paid_amount 	= this.selectedInvoice.paid;
				this.form.outstanding 	= this.selectedInvoice.outstanding;
			},

			setForm() {
				this.form.invoice_no 	= this.selectedPayment.invoice_no; 
				this.form.total 		= this.selectedPayment.amount;
				this.form.paid_amount 	= this.selectedPayment.paid;
				this.selectedType 		= this.selectedPayment.payment_method;
				this.form.outstanding 	= this.selectedPayment.outstanding;
			},

			submit() {
				this.isConfirming 	= true;
			},

			confirmSubmit() {
				this.isConfirming	= false;
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#payment-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedPayment ? "Edit payment - " + this.selectedPayment.invoice_no : "Create payment - " + this.selectedInvoice.invoice_no;
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedPayment ? "Update" : "Create";
			},

			url() {
				return this.selectedPayment ? "/payments/" + this.selectedPayment.id : "/payments";
			}
		},

		watch: {
			selectedType(newVal, oldVal) {
				this.form.type = newVal.value;
			},

		}


	}
</script>