<template>
	<div class="modal fade" id="customer-dialog" tabindex="-1" role="dialog">
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
									:editable="data.is_admin"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('branch_id')"
									@input="getBranches"
									ref="branches">
								</selector-input>
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
								<text-input v-model="form.name" 
									:defaultValue="form.name"
									:required="true"
									type="text"
									label="Name"
									name="name"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('name')">
								</text-input>
								<div class="row">
									<div class="col">
										<text-input v-model="form.contact" 
											:defaultValue="form.contact"
											:required="true"
											type="string"
											label="Contact"
											name="contact"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('contact')">
										</text-input>
									</div>
									<div class="col">
										<text-input v-model="form.registration_no" 
											:defaultValue="form.registration_no"
											:required="false"
											type="string"
											label="Registration/IC No"
											name="registration_no"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('registration_no')">
										</text-input>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<text-input v-model="form.email" 
											:defaultValue="form.email"
											:required="false"
											type="string"
											label="Email"
											name="email"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('email')">
										</text-input>
									</div>
									<div class="col">
										<text-input v-model="form.fax" 
											:defaultValue="form.fax"
											:required="false"
											type="string"
											label="Fax"
											name="fax"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('fax')">
										</text-input>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<text-input v-model="form.address1" 
											:defaultValue="form.address1"
											:required="false"
											type="string"
											label="Address line 1"
											name="address1"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('address1')">
										</text-input>
									</div>
									<div class="col">
										<text-input v-model="form.address2" 
											:defaultValue="form.address2"
											:required="false"
											type="string"
											label="Address line 2"
											name="address2"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('address2')">
										</text-input>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<text-input v-model="form.address3" 
											:defaultValue="form.address3"
											:required="false"
											type="string"
											label="Address line 3"
											name="address3"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('address3')">
										</text-input>
									</div>
									<div class="col">
										<text-input v-model="form.address4" 
											:defaultValue="form.address4"
											:required="false"
											type="string"
											label="Address line 4"
											name="address4"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('address4')">
										</text-input>
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
	</div>
</template>

<script>
	export default {
		props: {
		    data: {
		        type: Object
		    }
		  },
		data() {
			return {
				isActive: false,
				selectedCustomer: '',
				isEdit: false,
				form: new Form({
					type: 'Corporate',
					name: '',
					email: '',
					contact: '',
					fax: '',
					registration_no:'',
					address1:'',
					address2:'',
					address3:'',
					address4:'',
					branch_id:'',

				}),
				types: [
						{label: 'Corporate', value: 'Corporate'},
						{label: 'Walk-in', value: 'walk_in'},
						{label: 'Walk-in special', value: 'walk_in_special'},
						],

				selectedType: {label: 'Corporate', value: 'Corporate'},
				selectedBranch: '',
				selectedBranch_error: '',
				branches: [],
			};
		},

		mounted() {

			this.getBranches();

			window.events.$on('createCustomer', evt => this.createCustomer(evt));
			window.events.$on('editCustomer', evt => this.editCustomer(evt));

			$("#customer-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},
 
		methods: {
			createCustomer(evt) {
				this.openDialog();
			},

			editCustomer(evt) {
				this.selectedCustomer = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#customer-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedCustomer = '';
				this.form.reset();
			},

			setForm() {
				this.form.type   = this.selectedCustomer.type;
				this.form.name	 = this.selectedCustomer.name;
				this.form.email	 = this.selectedCustomer.email;
				this.form.fax	 = this.selectedCustomer.fax;
				this.form.contact = this.selectedCustomer.contact;
				this.form.registration_no =  this.selectedCustomer.registration_no;
				this.form.address1 = this.selectedCustomer.address1;
				this.form.address2 = this.selectedCustomer.address2;
				this.form.address3 = this.selectedCustomer.address3;
				this.form.address4 = this.selectedCustomer.address4;
				this.selectedBranch = {label: this.selectedCustomer.branch.name, value: this.selectedCustomer.branch.id};
				this.selectedType   = {label: this.selectedCustomer.type, value: this.selectedCustomer.type };
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				console.log("Success!");
				console.log(response);
				this.$emit("customerCreated", {customer: response.customer});

				$("#customer-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			},

			getBranches(){
				axios.get(this.branch_url)
					.then(response => this.setBranch(response))
					.catch(error => this.getBranches());
			},

			setBranch(response) {

				this.branches = response.data.map(function(branch){
					let obj = {};

					obj['value'] = branch.id;
					obj['label'] = branch.name;

					return obj;
				});

				if(this.branches.length == 1) {
					this.selectedBranch = {label: this.branches[0].label, value: this.branches[0].value };
				}

			},
		},

		computed: {
			title() {
				return this.selectedCustomer ? "Edit customer - " + this.selectedCustomer.name : "Create customer";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedCustomer ? "Update" : "Create";
			},

			url() {
				return this.selectedCustomer ? "/customers/" + this.selectedCustomer.id : "/customers";
			},

			branch_url()
			{
				return this.data.is_admin ? '/data/branches/': '/data/branch/' + this.data.current_branch ;
			} 
		},

		watch: {
			selectedType(newVal, oldVal) {
				this.form.type = newVal.value;
			},

			selectedBranch(newVal, oldVal) {
				this.form.branch_id = newVal.value;
			},

		}	

	}
</script>