<template>
	<div class="modal fade" id="product-type-dialog" tabindex="-1" role="dialog">
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
						</div>
						<div class="row mb-3">
							<div class="col form-inline">
								<checkbox-input v-model="form.is_merchandise"
									:defaultChecked="form.is_merchandise"
									label="Merchandise"
									name="is_merchandise"
									:editable="true">
								</checkbox-input>
								<div class="ml-2">
									<checkbox-input v-model="form.is_document"
										:defaultChecked="form.is_document"
										label="Document"
										name="is_document"
										:editable="true">
									</checkbox-input>
								</div>
								<div class="ml-2">
									<checkbox-input v-model="form.has_detail"
										:defaultChecked="form.has_detail"
										label="Has details"
										name="has_detail"
										:editable="true">
									</checkbox-input>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<selector-input :potentialData="vendors"
									v-model="selectedVendor" 
									:defaultData="selectedVendor"
									placeholder="Select default vendor"
									:required="false"
									label="Vendor"
									name="default_vendor_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('default_vendor_id')">
								</selector-input>
							</div>
							<div class="col">
								<selector-input :potentialData="zonetypes"
									v-model="selectedZoneType" 
									:defaultData="selectedZoneType"
									placeholder="Select default zone type"
									:required="false"
									label="Zone type"
									name="default_zone_type_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('default_zone_type_id')">
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
	export default {
		props: [''],
		data() {
			return {
				isActive: false,
				selectedVendor: "",
				selectedProductType: '',
				vendors: [],
				zonetypes: [],
				selectedZoneType: "",
				isEdit: false,
				form: new Form({
					name: '',
					is_document: '',
					is_merchandise: '',
					default_vendor_id: '',
					default_zone_type_id: '',
					has_detail: ''
				})
			};
		},

		mounted() {
			window.events.$on('createProductType', evt => this.createProductType(evt));
			window.events.$on('editProductType', evt => this.editProductType(evt));

			$("#vendor-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getZoneTypes();
		},

		methods: {
			getZoneTypes() {
				axios.get("/data/zonetypes")
					.then(response => this.setZoneTypes(response));
			},

			setZoneTypes(response) {
				this.zonetypes = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;

					return obj;
				});

				this.getVendors();
			},

			getVendors() {
				axios.get("/data/vendors")
					.then(response => this.setVendors(response));
			},

			setVendors(response) {
				this.vendors = response.data.map(function(type){
					let obj = {};

					obj['label'] = type.name;
					obj['value'] = type.id;

					return obj;
				});
			},

			createProductType(evt) {
				this.openDialog();
			},

			editProductType(evt) {
				this.selectedProductType = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#product-type-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedProductType = '';
			},

			setForm() {
				this.form.name = this.selectedProductType.name; 
				this.form.is_document = this.selectedProductType.is_document;
				this.form.is_merchandise = this.selectedProductType.is_merchandise;
				this.form.default_vendor_id = this.selectedProductType.default_vendor_id;				
				this.form.default_zone_type_id = this.selectedProductType.default_zone_type_id;
				this.form.has_detail = this.selectedProductType.has_detail;

				this.selectedVendor = '';
				this.selectedZoneType = '';

				if(this.form.default_vendor_id) {
					this.selectedVendor = _.filter(this.vendors, function(type){ return this.form.default_vendor_id == type.value; }.bind(this))[0];
				}

				if(this.form.default_zone_type_id) {
					this.selectedZoneType = _.filter(this.zonetypes, function(type){ return this.form.default_zone_type_id == type.value; }.bind(this))[0];
				}
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#product-type-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedProductType ? "Edit product type - " + this.selectedProductType.name : "Create product type";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedProductType ? "Update" : "Create";
			},

			url() {
				return this.selectedProductType ? "/admin/types/" + this.selectedProductType.id : "/admin/types";
			}
		},

		watch: {
			selectedZoneType(newVal, oldVal) {
				if(newVal) this.form.default_zone_type_id = newVal.value;
			},

			selectedVendor(newVal, oldVal) {
				if(newVal) this.form.default_vendor_id = newVal.value;
			}
		}


	}
</script>