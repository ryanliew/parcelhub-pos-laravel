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
	      		<div class="modal-body" v-if="data">
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
										<selector-input :potentialData="groups"
											v-model="selectedGroup" 
											:defaultData="selectedGroup"
											placeholder="Select group"
											:required="false"
											label="Customer group"
											name="customer_group_id"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('customer_group_id')">
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
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('name')">
										</text-input>
									</div>
									<div class="col">
										<text-input v-model="form.terms" 
											:defaultValue="form.terms"
											:required="false"
											type="number"
											label="Payment terms (days)"
											name="terms"
											:editable="true"
											:focus="false"
											:hideLabel="false"
											:error="form.errors.get('terms')">
										</text-input>
									</div>
								</div>
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
				selectedCustomer: '',
				selectedGroup: '',
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
					customer_group_id: '',
					terms: ''
				}),
				types: [
						{label: 'Corporate', value: 'Corporate'},
						{label: 'Walk-in', value: 'walk_in'},
						{label: 'Walk-in special', value: 'walk_in_special'},
						],
				groups: [],
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
				this.form.branch_id = this.selectedBranch.value;
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
				this.form.terms = this.selectedCustomer.terms;
				this.selectedBranch = {label: this.selectedCustomer.branch.name, value: this.selectedCustomer.branch.id};
				this.selectedType   = {label: this.selectedCustomer.type, value: this.selectedCustomer.type };

				this.selectedGroup = _.filter(this.groups, function(group){ return group.value == this.selectedCustomer.customer_group_id; }.bind(this))[0];
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
				this.$emit("customerCreated", {customer: response.customer});

				$("#customer-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			},

			getBranches(error = "No error", retry = 0){
				console.log("Get branches error");
				console.log(error);
				if(this.branch_url && retry < 3)
					axios.get(this.branch_url)
						.then(response => this.setBranch(response))
						.catch(error => this.getBranches(error, ++retry));
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
				}

				this.getGroups();
			},

			getGroups(error = "No error", retry = 0) {
				console.log("Get groups error");
				console.log(error);
				if(retry < 3) {
					axios.get("/data/groups?branch=" + this.selectedBranch.value)
						.then(response => this.setGroups(response))
						.catch(error => this.getGroups(error, ++retry));
				}
			},

			setGroups(response) {
				this.groups = response.data.map((group) => {
					let obj = {};

					obj['value'] = group.id;
					obj['label'] = group.name;

					return obj;
				});
			}
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
				if(this.data)
					return this.data.is_admin ? '/data/branches/': '/data/branch/' + this.data.current_branch ;

				return "";
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