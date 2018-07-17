<template>
	<div class="modal fade" id="terminal-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.float" 
									:defaultValue="form.float"
									:required="true"
									type="number"
									label="Float"
									name="float"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('float')">
								</text-input>
							</div>
							
						</div>
						
						<selector-input :potentialData="branches"
							v-model="selectedBranch" 
							:defaultData="selectedBranch"
							placeholder="Select branch"
							:required="true"
							label="Branch"
							name="branch_id"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('branch_id')">
						</selector-input>
						
						<checkbox-input v-model="form.is_active"
							:defaultChecked="form.is_active"
							label="Active"
							name="is_active"
							:editable="true">
						</checkbox-input>
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
				selectedTerminal: '',
				selectedBranch: '',
				isEdit: false,
				branches: [],
				form: new Form({
					name: '',
					float: '',
					branch_id: '',
					is_active: ''
				})
			};
		},

		mounted() {
			window.events.$on('createTerminal', evt => this.createTerminal(evt));
			window.events.$on('editTerminal', evt => this.editTerminal(evt));

			$("#terminal-dialog").on("hide.bs.modal", function(e){
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
			},
			
			createTerminal(evt) {
				this.openDialog();
				
			},

			editTerminal(evt) {
				this.selectedTerminal = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#terminal-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedTerminal = '';
			},

			setForm() {
				this.form.name = this.selectedTerminal.name;
				this.form.float = this.selectedTerminal.float;
				this.form.branch_id = this.selectedTerminal.branch_id;
				this.form.is_active = this.selectedTerminal.is_active;

				this.selectedBranch = '';

				if(this.form.branch_id) {
					this.selectedBranch = _.filter(this.branches, function(type){ return this.form.branch_id == type.value; }.bind(this))[0];
				}
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#terminal-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedTerminal ? "Edit terminal - " + this.selectedTerminal.name : "Create terminal";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedTerminal ? "Update" : "Create";
			},

			url() {
				return this.selectedTerminal ? "/admin/terminals/" + this.selectedTerminal.id : "/admin/terminals";
			}
		},

		watch: {
			selectedBranch(newVal, oldVal) {
				this.form.branch_id = newVal.value;
			}
		}


	}
</script>