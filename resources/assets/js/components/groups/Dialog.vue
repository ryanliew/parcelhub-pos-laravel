<template>
	<div class="modal fade" id="customer-group-dialog" tabindex="-1" role="dialog">
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
								<selector-input :potentialData="branches"
									v-model="selectedBranch" 
									:defaultData="selectedBranch"
									placeholder="Select branch"
									:required="true"
									label="Branch"
									name="branch_id"
									:editable="user.is_admin && !isEdit"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('branch_id')"
									ref="branches">
								</selector-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.name" 
									:defaultValue="form.name"
									:required="true"
									type="text"
									label="Name"
									name="name"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('name')">
								</text-input>
							</div>
						</div>
						<div class="row" v-if="selectedBranch">
							<div class="col">
								<div class="customers-multi-select">
									<div class="customers-wrapper">
										<div class="customer-wrapper" v-for="(customer, index) in customers" @click.prevent="select(index, $event)">
											<div class="customer selected" v-if="customer.selected">
												{{ customer.label }}
											</div>
											<div class="customer" v-else>
												{{ customer.label }}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-auto text-center">
								<div class="btn btn-secondary mb-3" @click="commitAll">
									<i class="fas fa-angle-double-right"></i>
								</div>
								<br>
								<div class="btn btn-secondary mb-3" @click="commit">
									<i class="fas fa-angle-right"></i>
								</div>
								<br>
								<div class="btn btn-secondary mb-3" @click="uncommit">
									<i class="fas fa-angle-left"></i>
								</div>
								<br>
								<div class="btn btn-secondary" @click="uncommitAll">
									<i class="fas fa-angle-double-left"></i>
								</div>
							</div>
							<div class="col">
								<div class="customers-multi-select">
									<div class="customers-wrapper">
										<div class="customer-wrapper" v-for="(customer, index) in selectedCustomers" @click.prevent="selectSelected(index, $event)">
											<div class="customer selected" v-if="customer.selected">
												{{ customer.label }}
											</div>
											<div class="customer" v-else>
												{{ customer.label }}
											</div>
										</div>
									</div>
								</div>
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
		props: ['user'],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedGroup: '',
				customers: [],
				branches: [],
				selectedBranch: '',
				selectedCustomers: [],
				isEdit: false,
				form: new Form({
					branch_id: '',
					name: '',
					customers: [],
					id: ''
				}),

			};
		},

		mounted() {

			this.getBranches();

			window.events.$on('createCustomerGroup', evt => this.createCustomerGroup(evt));
			window.events.$on('editCustomerGroup', evt => this.editCustomerGroup(evt));

			$("#customer-group-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
			getBranches(error = "No error", retry = 3){
				if(this.branch_url && retry <= 3) {
					// console.log(error.label);
					axios.get(this.branch_url)
						.then(response => this.setBranch(response))
						.catch(error => this.getBranches(error, ++retry));
				}
			},

			setBranch(response) {
				if(response.data) {
					this.branches = response.data.map(function(branch){
						let obj = {};

						obj['value'] = branch.id;
						obj['label'] = branch.name;

						return obj;
					});

					if(this.branches.length == 1) {
						this.selectedBranch = {label: this.branches[0].label, value: this.branches[0].value };
						this.form.branch_id = this.branches[0].value;
					}

					if(this.user.is_admin) {
						this.selectedBranch = _.filter(this.branches, function(branch){ return branch.value == this.user.current_branch; }.bind(this))[0];
					}

					if(this.isEdit) this.setSelectedBranch();
				}
			},

			getCustomers(error = "No error", retry = 0) {
				if(retry <= 3)
					axios.get("/data/customers?groups=1&branch=" + this.selectedBranch.value)
						.then(response => this.setCustomers(response))
						.catch(error => this.getCustomers(error, ++retry));
			},

			setCustomers(response) {
				this.customers = response.data.map(function(customer){
					let obj = {};

					obj['value'] = customer.id;
					obj['label'] = customer.name;
					obj['selected'] = false;

					return obj;
				});
			},

			createCustomerGroup(evt) {
				this.openDialog();	
			},

			editCustomerGroup(evt) {
				this.isEdit = true;
				this.selectedGroup = evt[0];
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#customer-group-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				// console.log("Closing dialog");
				this.isActive = false;
				this.selectedCustomers = [];
				this.selectedGroup = '';
				this.customers = [];
				this.selectedBranch = '';
				this.form.name = '';

				this.isEdit = false;
				this.form.reset();
			},

			setForm() {
				this.form.id = this.selectedGroup.id;
				this.form.name = this.selectedGroup.name;

				this.setSelectedBranch();
				
				Vue.nextTick(() => this.setSelectedCustomers());
				
			},

			setSelectedCustomers() {
				this.selectedCustomers = this.selectedGroup.customers.map((customer) => {
					let obj = {};

					obj['label'] = customer.name;
					obj['value'] = customer.id;
					obj['selected'] = false;

					return obj;
				});
			},

			setSelectedBranch() {
				this.selectedBranch = _.filter(this.branches, function(branch){ return branch.value == this.selectedGroup.branch_id; }.bind(this))[0];
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
				$("#customer-group-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			},

			select(customer, evt) {
				if(evt.shiftKey) this.bulkSelect(customer);
				else this.customers[customer].selected = !this.customers[customer].selected;
			},

			bulkSelect(customer) {

				let lastSelected = _.findLastIndex(this.customers, function(customer){ return customer.selected; });

				let input = {
					last: lastSelected,
					customer: customer
				};

				this.customers.forEach(function(customer, index){
					if(index >= input.last && index <= input.customer) customer.selected = true;
				}.bind(input));
			},

			selectSelected(customer, evt) {
				if(evt.shiftKey) this.bulkSelectSelected(customer);
				else this.selectedCustomers[customer].selected = !this.selectedCustomers[customer].selected;
			},

			bulkSelectSelected(customer) {

				let lastSelected = _.findLastIndex(this.selectedCustomers, function(customer){ return customer.selected; });

				let input = {
					last: lastSelected,
					customer: customer
				};

				this.selectedCustomers.forEach(function(customer, index){
					if(index >= input.last && index <= input.customer) customer.selected = true;
				}.bind(input));
			},

			commit() {
				this.customers.forEach(function(customer){
					if(customer.selected) {
						customer.selected = false;
						this.selectedCustomers.push(customer);
					}
				}.bind(this));

				this.customers = _.difference(this.customers, this.selectedCustomers);
			},

			uncommit() {
				this.selectedCustomers.forEach(function(customer){
					if(customer.selected) {
						customer.selected = false;
						this.customers.push(customer);
					}
				}.bind(this));

				this.selectedCustomers = _.difference(this.selectedCustomers, this.customers);
			},

			commitAll() {
				this.customers.forEach(function(customer){
					this.selectedCustomers.push(customer);
				}.bind(this));

				this.customers = [];
			},

			uncommitAll() {
				this.selectedCustomers.forEach(function(customer){
					this.customers.push(customer);
				}.bind(this));

				this.selectedCustomers = [];
			}


		},

		computed: {
			title() {
				return this.isEdit ? "Edit customer group - " + this.selectedGroup.name : "Create customer group";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.isEdit ? "Update" : "Create";
			},

			url() {
				return this.isEdit ? "/groups/" + this.selectedGroup.id : "/groups";
			},

			branch_url() {
				if(this.user)
					return this.user.is_admin ? '/data/branches/': '/data/branch/' + this.user.current_branch ;

				return "";
			}
		},

		watch: {
			selectedBranch(val) {
				// console.log("Clearing customers");
				this.customers = [];
				this.selectedCustomers = [];
				if(val) {
					this.form.branch_id = val.value;
					this.getCustomers();
				}
			},

			selectedCustomers(val) {
				this.form.customers = val;
			}
		}
	}
</script>