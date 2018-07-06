<template>
	<div class="modal fade" id="zone-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.state" 
									:defaultValue="form.state"
									:required="true"
									type="text"
									label="State"
									name="state"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('state')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.zone" 
									:defaultValue="form.zone"
									:required="true"
									type="number"
									label="Zone"
									name="zone"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('zone')">
								</text-input>
							</div>
							
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.postcode_start" 
									:defaultValue="form.postcode_start"
									:required="true"
									type="number"
									label="Postcode start"
									name="postcode_start"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('postcode_start')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.postcode_end" 
									:defaultValue="form.postcode_end"
									:required="true"
									type="number"
									label="Postcode start"
									name="postcode_end"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('postcode_end')">
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
				selectedZone: '',
				selectedZoneType: '',
				isEdit: false,
				zonetypes: [],
				form: new Form({
					state: '',
					zone: '',
					postcode_start: '',
					postcode_end: ''
				})
			};
		},

		mounted() {
			window.events.$on('createZone', evt => this.createZone(evt));
			window.events.$on('editZone', evt => this.editZone(evt));
		},

		methods: {
			createZone(evt) {
				this.openDialog();
				
			},

			editZone(evt) {
				this.selectedZone = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#zone-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				$("#zone-dialog").modal('hide');
				this.isActive = false;
			},

			setForm() {
				this.form.state = this.selectedZone.state; 
				this.form.postcode_start = this.selectedZone.postcode_start;
				this.form.postcode_end = this.selectedZone.postcode_end;
				this.form.zone = this.selectedZone.zone;
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedZone ? "Edit zone - " + this.selectedZone.name : "Create zone";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedZone ? "Update" : "Create";
			},

			url() {
				return this.selectedZone ? "/admin/zones/" + this.selectedZone.id : "/admin/zones";
			}
		},

		watch: {
			selectedZoneType(newVal, oldVal) {
				this.form.zone_type_id = newVal.value;
			}
		}


	}
</script>