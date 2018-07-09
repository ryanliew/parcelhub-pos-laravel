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
								<text-input v-model="form.invoice_no" 
									:defaultValue="form.invoice_no"
									:required="true"
									type="text"
									label="Invoice No."
									name="invoice_no"
									:editable="false"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('invoice_no')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.total" 
									:defaultValue="form.total"
									:required="true"
									type="text"
									label="Total"
									name="total"
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
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('outstanding')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.remaining" 
									:defaultValue="form.remaining"
									:required="true"
									type="text"
									label="Remaining"
									name="remaining"
									:editable="false"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('remaining')">
								</text-input>
							</div>
						</div>
						<div class="row">
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
	</div>
</template>

<script>
	export default {
		props: [''],
		data() {
			return {
				isActive: false,
				selectedPayment: '',
				isEdit: false,
				form: new Form({
					invoice_no: '',
					total: '',
					paid_amount: '',
					remaining: '',
					outstanding: '',
					amount: '',

				})
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
				this.selectedPayment = evt[0];
				this.setForm();
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

			setForm() {
				this.form.invoice_no = this.selectedPayment.id; 
				this.form.total = this.selectedPayment.total;
				this.form.paid_amount = this.selectedPayment.payment;
				this.form.remaining = this.selectedPayment.remaining;
				this.form.outstanding = this.selectedPayment.outstanding;
				this.form.amount = this.selectedPayment.amount;
			},

			submit() {
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
				return this.selectedPayment ? "Edit payment - " + this.selectedPayment.invoice_no : "Create payment";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedPayment ? "Update" : "Create";
			},

			url() {
				return this.selectedPayment ? "/admin/branches/" + this.selectedPayment.id : "/payments";
			}
		}


	}
</script>