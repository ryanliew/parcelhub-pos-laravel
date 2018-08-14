<template>
	<div class="modal fade" id="permission-dialog" tabindex="-1" role="dialog">
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
								<selector-input :potentialData="users"
									v-model="selectedUser" 
									:defaultData="selectedUser"
									placeholder="Select user"
									:required="true"
									label="User"
									name="user_id"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('user_id')">
								</selector-input>	
							</div>
							<div class="col">
								<selector-input :potentialData="levels"
									v-model="selectedLevel" 
									:defaultData="selectedLevel"
									placeholder="Select access level"
									:required="true"
									label="Access"
									name="access_level"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('access_level')">
								</selector-input>
							</div>
						</div>
						
						<selector-input :potentialData="branches"
							v-model="selectedBranch" 
							:defaultData="selectedBranch"
							placeholder="Select branch"
							:required="true"
							label="Branch"
							name="branch_id"
							:editable="canEditBranch"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('branch_id')">
						</selector-input>
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
		props: ['default_branch'],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedUser: '',
				selectedBranch: '',
				selectedLevel: '',
				selectedPermission: '',

				isEdit: false,
				branches: [],
				levels: [{value: 'read', label: 'Cashier'}, {value:'write', label:'Branch admin'}],
				users: [],

				form: new Form({
					user_id: '',
					branch_id: '',
					access_level: ''
				})
			};
		},

		mounted() {
			window.events.$on('createPermission', evt => this.createPermission(evt));
			window.events.$on('editPermission', evt => this.editPermission(evt));

			$("#permission-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getBranches();
		},

		methods: {
			getBranches() {
				axios.get("/data/branches")
					.then(response => this.setBranches(response))
					.catch(error =>this.getBranches());
			},

			setBranches(response) {
				this.branches = response.data.map(function(branch){
					let obj = {};

					obj['label'] = branch.name;
					obj['value'] = branch.id;

					return obj;
				});

				this.form.branch_id = this.default_branch;

				if(this.form.branch_id) {
					this.selectedBranch = _.filter(this.branches, function(branch){ return this.form.branch_id == branch.value; }.bind(this))[0];
				}
				this.getUsers();
			},

			getUsers(error = 'No error') {
				axios.get("/data/users")
					.then(response => this.setUsers(response))
					.catch(error =>this.getUsers(error));
			},

			setUsers(response) {
				this.users = response.data.map(function(user){
					let obj = {};

					obj['label'] = user.name;
					obj['value'] = user.id;

					return obj;
				});
			},
			
			createPermission(evt) {
				this.openDialog();
				
			},

			editPermission(evt) {
				this.selectedPermission = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#permission-dialog").modal();
				this.isActive = true;
				
				this.form.branch_id = this.default_branch;

				if(this.form.branch_id) {
					this.selectedBranch = _.filter(this.branches, function(branch){ return this.form.branch_id == branch.value; }.bind(this))[0];
				}
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedPermission = '';
				this.selectedUser = '';
				this.selectedBranch = '';
				this.selectedLevel = '';
			},

			setForm() {
				this.form.branch_id = this.selectedPermission.branch_id;
				this.form.user_id = this.selectedPermission.user_id;
				this.form.access_level = this.selectedPermission.type;

				this.selectedBranch = '';
				this.selectedUser = '';
				this.selectedLevel = '';

				if(this.form.branch_id) {
					this.selectedBranch = _.filter(this.branches, function(branch){ return this.form.branch_id == branch.value; }.bind(this))[0];
				}

				if(this.form.access_level) {
					this.selectedLevel = _.filter(this.levels, function(level){ return this.form.access_level == level.value; }.bind(this))[0];
				}

				if(this.form.user_id) {
					this.selectedUser = _.filter(this.users, function(user){ return this.form.user_id == user.value; }.bind(this))[0];
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
				$("#permission-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedPermission ? "Edit permission - " + this.selectedUser.label : "Create permission";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedPermission ? "Update" : "Create";
			},

			url() {
				return this.selectedPermission ? "/admin/permissions/" + this.selectedPermission.id : "/admin/permissions";
			},

			canEditBranch() {
				return this.default_branch ? false : true;
			}
		},

		watch: {
			selectedBranch(newVal, oldVal) {
				if(newVal)
					this.form.branch_id = newVal.value;
			},

			selectedUser(newVal, oldVal) {
				if(newVal)
					this.form.user_id = newVal.value;
			},

			selectedLevel(newVal, oldVal) {
				if(newVal)
					this.form.access_level = newVal.value;
			},
		}


	}
</script>