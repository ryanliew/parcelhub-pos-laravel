<template>
	<div class="modal fade" id="vendor-dialog" tabindex="-1" role="dialog">
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
							<!-- <div class="col">
								<selector-input :potentialData="zonetypes"
									v-model="selectedZoneType" 
									:defaultData="selectedZoneType"
									placeholder="Select zone type"
									:required="true"
									label="Zone Type"
									name="zone_type_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('zone_type_id')">
								</selector-input>
							</div> -->
							
						</div>
						<!-- <div class="row">
							<div class="col">
								<text-input v-model="form.formula" 
									:defaultValue="form.formula"
									:required="true"
									type="text"
									label="Formula"
									name="formula"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('formula')">
								</text-input>
							</div>
						</div> -->
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
		props: [''],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedVendor: '',
				selectedZoneType: '',
				isEdit: false,
				zonetypes: [],
				form: new Form({
					name: '',
					// formula: '',
					// zone_type_id: '',
				})
			};
		},

		mounted() {
			window.events.$on('createVendor', evt => this.createVendor(evt));
			window.events.$on('editVendor', evt => this.editVendor(evt));

			$("#vendor-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			// this.getZoneType();
		},

		methods: {
			getZoneType() {
				axios.get("/data/zonetypes")
					.then(response => this.setZoneType(response));
			},

			setZoneType(response) {
				this.zonetypes = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;

					return obj;
				});

				if(this.form.zone_type_id) {
					this.selectedZoneType = _.filter(this.zonetypes, function(type){ return this.form.zone_type_id == type.value; }.bind(this))[0];
				}
			},

			createVendor(evt) {
				this.openDialog();
				
			},

			editVendor(evt) {
				this.selectedVendor = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#vendor-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedVendor = '';
			},

			setForm() {
				this.form.name = this.selectedVendor.name; 
				this.form.formula = this.selectedVendor.formula;
				this.form.zone_type_id = this.selectedVendor.zone_type_id;

				if(this.form.zone_type_id) {
					this.selectedZoneType = _.filter(this.zonetypes, function(type){ return this.form.zone_type_id == type.value; }.bind(this))[0];
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
				$("#vendor-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedVendor ? "Edit vendor - " + this.selectedVendor.name : "Create vendor";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedVendor ? "Update" : "Create";
			},

			url() {
				return this.selectedVendor ? "/admin/vendors/" + this.selectedVendor.id : "/admin/vendors";
			}
		},

		watch: {
			selectedZoneType(newVal, oldVal) {
				this.form.zone_type_id = newVal.value;
			}
		}


	}
</script>