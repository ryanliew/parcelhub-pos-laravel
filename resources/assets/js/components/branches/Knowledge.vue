<template>
	<div class="modal fade" id="branch-knowledge-dialog" tabindex="-1" role="dialog">
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
									placeholder="Select a branch"
									:required="true"
									label="Branch"
									name="branch_code"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('branch_code')">
								</selector-input>
							</div>
							<div class="col">
								<selector-input :potentialData="types"
									v-model="selectedType" 
									:defaultData="selectedType"
									placeholder="Select product type"
									:required="true"
									label="Product Type"
									name="product_type"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('product_type')">
								</selector-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<selector-input :potentialData="zonetypes"
									v-model="selectedZoneType" 
									:defaultData="selectedZoneType"
									placeholder="Select zone type"
									:required="true"
									label="Zone Type"
									name="zone_type"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('zone_type')">
								</selector-input>
							</div>
							<div class="col">
								<selector-input :potentialData="vendors"
									v-model="selectedVendor" 
									:defaultData="selectedVendor"
									placeholder="Select vendor"
									:required="true"
									label="Vendor Name"
									name="vendor_name"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('vendor_name')">
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
		props: [''],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedKnowledge: '',
				isEdit: false,
				branches: [],
				types: [],
				vendors: [],
				zonetypes: [],
				selectedBranch: '',
				selectedType: '',
				selectedVendor: '',
				selectedZoneType: '',
				form: new Form({
					branch_code: '',
					product_type: '',
					zone_type: 'Domestic',
					vendor_name: ''
				})
			};
		},

		mounted() {
			window.events.$on('createBranchKnowledge', evt => this.createKnowledge(evt));
			window.events.$on('editBranchKnowledge', evt => this.editKnowledge(evt));

			$("#branch-knowledge-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getBranches();
		},

		methods: {

			getBranches() {
				axios.get("/data/branches")
					.then(response => this.setBranches(response))
					.catch(error => this.getBranches());
			},

			setBranches(response) {
				this.branches = response.data.map(function(branch){
					let obj = {};

					obj['value'] = branch.code;
					obj['label'] = branch.name;

					return obj;
				});

				this.branches.unshift({value: "*", label: "Any"});

				this.getProductTypes();
			},

			getProductTypes() {
				axios.get("/data/producttypes")
					.then(response => this.setProductTypes(response))
					.catch(error => this.getProductTypes());
			},

			setProductTypes(response){
				this.types = response.data.map(function(type){
					let obj = {};

					obj['value'] = type.name;
					obj['label'] = type.name;

					return obj;
				});

				this.types.unshift({value: "*", label: "Any"});

				this.getZoneTypes();

			},

			getZoneTypes() {
				axios.get("/data/zonetypes")
					.then(response => this.setZoneTypes(response))
					.catch(error => this.getZoneTypes());
			},

			setZoneTypes(response) {
				this.zonetypes = response.data.map(function(type){
					let obj = {};

					obj['value'] = type.name;
					obj['label'] = type.name;

					return obj;
				});


				this.getVendors();
			},

			getVendors() {
				axios.get("/data/vendors")
					.then(response => this.setVendors(response))
					.catch(error => this.getVendors());
			},

			setVendors(response) {
				this.vendors = response.data.map(function(vendor){
					let obj = {};

					obj['value'] = vendor.name;
					obj['label'] = vendor.name;

					return obj;
				});
			},

				
			createKnowledge(evt) {

				this.selectedBranch = '';
				this.selectedVendor = '';
				this.selectedType = '';
				this.selectedZoneType = '';
				
				this.openDialog();
				
			},

			editKnowledge(evt) {
				this.selectedKnowledge = evt[0];
				this.isEdit = true;


				this.selectedBranch = '';
				this.selectedVendor = '';
				this.selectedType = '';
				this.selectedZoneType = '';

				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#branch-knowledge-dialog").modal();
				this.selectedZoneType = {value: "Domestic", label: "Domestic"};
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedKnowledge = '';
			},

			setForm() {
				this.form.branch_code = this.selectedKnowledge.branch_code;
				this.form.product_type = this.selectedKnowledge.product_type;
				this.form.zone_type = this.selectedKnowledge.zone_type;
				this.form.vendor_name = this.selectedKnowledge.vendor_name;

				this.selectedBranch = _.filter(this.branches, function(branch){ return this.form.branch_code == branch.value; }.bind(this))[0];
				this.selectedType = _.filter(this.types, function(type){ return this.form.product_type == type.value; }.bind(this))[0];
				this.selectedZoneType = _.filter(this.zonetypes, function(zone){ return this.form.zone_type == zone.value; }.bind(this))[0];
				this.selectedVendor = _.filter(this.vendors, function(vendor){ return this.form.vendor_name == vendor.value; }.bind(this))[0];
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
				$("#branch-knowledge-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedKnowledge ? "Edit knowledge" : "Create knowledge";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedKnowledge ? "Update" : "Create";
			},

			url() {
				return this.selectedKnowledge ? "/admin/branch/knowledge/" + this.selectedKnowledge.id : "/admin/branch/knowledge";
			}
		},

		watch: {
			selectedZoneType(newVal, oldVal){
				if(newVal)
					this.form.zone_type = newVal.value;
			},

			selectedVendor(newVal, oldVal){
				if(newVal)
					this.form.vendor_name = newVal.value;
			},

			selectedType(newVal, oldVal){
				if(newVal)
					this.form.product_type = newVal.value;
			},

			selectedBranch(newVal, oldVal){
				if(newVal)
					this.form.branch_code= newVal.value;
			},
		}


	}
</script>