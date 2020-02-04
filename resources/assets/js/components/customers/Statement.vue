<style>
	.col.cust-input {
		max-width: 250px;
	}

	.cust-input .select-label {
		margin-bottom: 7px;
	}

	.cust-input .v-select input[type=search], .cust-input .v-select input[type=search]:focus
	{
		height: 37px;
	}
</style>
<template>
	<div class="modal fade" id="customer-statement" tabindex="-1" role="dialog">
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
								<text-input v-model="form.date_from" 
									:defaultValue="form.date_from"
									:required="true"
									type="date"
									label="Date from:"
									name="date_from"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:disabled="this.isMultiple"
									:error="form.errors.get('date_from')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.date_to" 
									:defaultValue="form.date_to"
									:required="true"
									type="date"
									label="Date to:"
									name="date_to"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:disabled="this.isMultiple"
									:error="form.errors.get('date_to')">
								</text-input>
							</div>
							<div class="col cust-input small-select">
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
	import moment from 'moment';
	export default {
		props: {
		    data: {
				type: Object,
			}, 
			isMultiple: {
				type: Boolean,
			},
			from: {
				type: "",
			},
			to: {
				type: "",
			},
		  },
		data() {
			return {
				form: new Form({
					date_to: this.isMultiple? this.to : moment().format("YYYY-MM-DD"),
					date_from: this.isMultiple? this.from : moment().startOf('month').format("YYYY-MM-DD"),
					type: 'All',
					customers: [],
				}),
				selected_customer:'',
				selectedType: {label: 'All', value: 'All'},
				types: [
						{label: 'All', value: 'All'},
						{label: 'Outstanding', value: 'Outstanding'},
						],
			};
		},

		mounted() {
			window.events.$on('generateStatement', evt => this.generateStatement(evt));

			$("#customer-statement").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},
 
		methods: {
			generateStatement(evt) {
				if( this.isMultiple ){
					this.form.customers = [];
					evt.forEach(element => {
						this.form.customers.push(element);
					});
				}
				else {
					this.selected_customer = evt[0];
				}
			    this.openDialog();
			},

			openDialog() {
				$("#customer-statement").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedType = {label: 'All', value: 'All'};
				this.form.reset();
			},


			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {

				$("#customer-statement").modal('hide');

				this.closeDialog();

				if( this.isMultiple ){
					response['id'].forEach(element => {
					window.open("/customers/statement/" + element + '/' + response.start + '/' + response.end, '_blank');					
					});
				}
				else {
					window.open("/customers/statement/" + response.id + '/' + response.start + '/' + response.end, '_blank');
				}	
			},

		},

		computed: {
			title() {
				return "Generate account of statement";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return "Generate statement";
			},

			url() {
				return this.isMultiple? "/customers/statement_multiple/" : "/customers/statement/" + this.selected_customer.id;
			},
		},

		watch: {
			selectedType(newVal, oldVal) {
				this.form.type = newVal.value;
			},

		}

	}
</script>