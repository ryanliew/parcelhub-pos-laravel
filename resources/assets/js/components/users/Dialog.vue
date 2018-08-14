<template>
	<div class="modal fade" id="user-dialog" tabindex="-1" role="dialog">
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
							<div class="col">
								<text-input v-model="form.username" 
									:defaultValue="form.username"
									:required="true"
									type="text"
									label="Username"
									name="username"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('username')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.email" 
									:defaultValue="form.email"
									:required="true"
									type="email"
									label="Email"
									name="email"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('email')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.password" 
									:defaultValue="form.password"
									:required="true"
									type="password"
									label="Password"
									name="password"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('password')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.password_confirmation" 
									:defaultValue="form.password_confirmation"
									:required="true"
									type="password"
									label="Confirm Password"
									name="password_confirmation"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('password_confirmation')">
								</text-input>
							</div>
						</div>
						<div class="row" v-if="is_admin">
							<div class="col">
								<selector-input :potentialData="branches"
									v-model="selectedBranch" 
									:defaultData="selectedBranch"
									placeholder="Select default branch"
									:required="true"
									label="Branch"
									name="current_branch"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('current_branch')">
								</selector-input>
							</div>
							<div class="col">
								<selector-input :potentialData="terminals"
									v-model="selectedTerminal" 
									:defaultData="selectedTerminal"
									placeholder="Select a terminal"
									:required="true"
									label="Terminal"
									name="current_terminal"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('current_terminal')">
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
	  	<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
	</div>
</template>

<script>
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
	
	export default {
		props: ['is_admin'],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedBranch: '',
				selectedTerminal: '',
				selectedUser: '',
				branches: [],
				terminals: [],
				isEdit: false,
				form: new Form({
					name: '',
					username: '',
					email: '',
					password: '',
					password_confirmation: '',
					current_terminal: '',
					current_branch: '',
				})
			};
		},

		mounted() {
			window.events.$on('createUser', evt => this.createUser(evt));
			window.events.$on('editUser', evt => this.editUser(evt));

			$("#user-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getBranches();
		},

		methods: {
			getBranches() {
				axios.get("/data/branches")
					.then(response => this.setBranches(response));
			},

			setBranches(response) {
				this.branches = response.data.map(function(branch){
					let obj = {};

					obj['value'] = branch.id;
					obj['label'] = branch.name;

					return obj;
				});

				if(this.form.current_branch) {
					this.selectedBranch = _.filter(this.branches, function(type){ return this.form.current_branch == type.value; }.bind(this))[0];
				}
			},

			getTerminals(error = 'No error') {
				// console.log(error);
				axios.get("/data/branch/" + this.selectedBranch.value + "/terminals")
					.then(response => this.setTerminals(response))
					.catch(error => this.getTerminals(error));
			},

			setTerminals(response) {
				this.terminals = response.data.map(function(terminal){
					let obj ={};

					obj['value'] = terminal.id;
					obj['label'] = terminal.name;

					return obj;
				});

				if(this.form.current_terminal) {
					this.selectedTerminal = _.filter(this.terminals, function(terminal){ return this.form.current_terminal == terminal.value; }.bind(this))[0];
				}
			},

			createUser(evt) {
				this.openDialog();
			},

			editUser(evt) {
				this.selectedUser = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#user-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedUser = '';
				this.selectedBranch = '';
				this.selectedTerminal = '';
				this.form.reset();
			},

			setForm() {
				this.form.name = this.selectedUser.name; 
				this.form.email = this.selectedUser.email;
				this.form.username = this.selectedUser.username;
				this.form.current_branch = this.selectedUser.current.id;
				this.form.current_terminal = this.selectedUser.current_terminal;

				if(this.branches.length > 0) {
					this.selectedBranch = _.filter(this.branches, function(branch){ return this.form.current_branch == branch.value; }.bind(this))[0];
					this.getTerminals();
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
				$("#user-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedUser ? "Edit user - " + this.selectedUser.name : "Create user";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedUser ? "Update" : "Create";
			},

			url() {
				return this.selectedUser ? "/admin/users/" + this.selectedUser.id : "/admin/users";
			}
		},

		watch: {
			selectedUserType(newVal, oldVal) {
				this.form.user_type_id = newVal.value;
			},

			selectedBranch(newVal, oldVal) {
				this.terminals = [];
				this.selectedTerminal = "";

				if(newVal) {
					this.form.current_branch = newVal.value;
					this.getTerminals();
				}
			},

			selectedTerminal(newVal, oldVal) {
				if(newVal)
					this.form.current_terminal = newVal.value;
			}
		}


	}
</script>