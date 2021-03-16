<template>
	<div class="modal fade" id="branch-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.code" 
									:defaultValue="form.code"
									:required="true"
									type="text"
									label="Code"
									name="code"
									:editable="!isEdit"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('code')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.address" 
									:defaultValue="form.address"
									:required="true"
									type="text"
									label="Address"
									name="address"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('address')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.registration_no" 
									:defaultValue="form.registration_no"
									:required="true"
									type="text"
									label="Registration No."
									name="registration_no"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('registration_no')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.gst_no" 
									:defaultValue="form.gst_no"
									:required="false"
									type="text"
									label="GST No."
									name="gst_no"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('gst_no')">
								</text-input>
							</div>
						</div>	
						<div class="row">
							<div class="col">
								<text-input v-model="form.fax" 
									:defaultValue="form.fax"
									:required="false"
									type="text"
									label="Fax No."
									name="fax"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('fax')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.tollfree" 
									:defaultValue="form.tollfree"
									:required="false"
									type="text"
									label="Toll-free No."
									name="tollfree"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('tollfree')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.website" 
									:defaultValue="form.website"
									:required="false"
									type="text"
									label="Website"
									name="website"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('website')">
								</text-input>
							</div>
							<div class="col">
								<selector-input :potentialData="types"
									v-model="selectedType" 
									:defaultData="selectedType"
									placeholder="Select default product type"
									:required="true"
									label="Default product type"
									name="product_type_id"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('product_type_id')">
								</selector-input>
							</div>
						</div>
            <div class="row">
              <div class="col">
                <text-input v-model="form.lc_code"
                            :defaultValue="form.lc_code"
                            :required="false"
                            type="text"
                            label="LC Marking"
                            name="lc_code"
                            :editable="true"
                            :focus="false"
                            :hideLabel="false"
                            :error="form.errors.get('lc_code')">
                </text-input>
              </div>
              <div class="col">
                <text-input v-model="form.contact_emails"
                            :defaultValue="form.contact_emails"
                            :required="false"
                            type="text"
                            label="Contact Email"
                            name="contact_emails"
                            :editable="true"
                            :focus="false"
                            :hideLabel="false"
                            :error="form.errors.get('contact_emails')">
                </text-input>
              </div>
            </div>
						<hr>
						<h5>Owner information</h5>
						<div class="row">
							<div class="col">
								<text-input v-model="form.owner" 
									:defaultValue="form.owner"
									:required="true"
									type="text"
									label="Business display name"
									name="owner"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('owner')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.registered_company_name" 
									:defaultValue="form.registered_company_name"
									:required="true"
									type="text"
									label="Registered company name"
									name="registered_company_name"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('registered_company_name')">
								</text-input>
							</div>
						</div>
						<div class="row">
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
							<div class="col">
								<text-input v-model="form.contact" 
									:defaultValue="form.contact"
									:required="true"
									type="text"
									label="Contact number"
									name="contact"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('contact')">
								</text-input>
							</div>
						</div>
						<hr>
						<h5>Payment information</h5>
						<div class="row">
							<div class="col">
								<text-input v-model="form.payment_bank" 
									:defaultValue="form.payment_bank"
									:required="true"
									type="text"
									label="Bank Name"
									name="payment_bank"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('payment_bank')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.payment_acc_no" 
									:defaultValue="form.payment_acc_no"
									:required="true"
									type="text"
									label="Account No"
									name="payment_acc_no"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('payment_acc_no')">
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
	  	<confirmation :message="confirm_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
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
				selectedBranch: '',
				types: [],
				selectedType: '',
				isEdit: false,
				form: new Form({
					name: '',
					code: '',
					owner: '',
					contact: '',
					email: '',
					registration_no: '',
					payment_bank: '',
					payment_acc_no: '',
					gst_no: '',
					fax: '',
					tollfree: '',
					website: '',
					address: '',
					default_product_type: '',
					product_type_id: '',
					registered_company_name: '',
          lc_code: '',
          contact_emails: '',
				})
			};
		},

		mounted() {
			window.events.$on('createBranch', evt => this.createBranch(evt));
			window.events.$on('editBranch', evt => this.editBranch(evt));

			$("#branch-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));

			this.getProductTypes();
		},

		methods: {
			getProductTypes() {
				axios.get("/data/producttypes")
					.then(response => this.setProductTypes(response))
					.catch(error => this.getProductTypes());
			},

			setProductTypes(response){
				this.types = response.data.map(function(type){
					let obj = {};

					obj['value'] = type.id;
					obj['label'] = type.name;

					return obj;
				});
			},

			createBranch(evt) {
				this.isEdit = false;

				this.selectedType = '';
				
				this.openDialog();
				
			},

			editBranch(evt) {
				this.selectedBranch = evt[0];
				this.isEdit = true;

				this.selectedType = '';
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#branch-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedBranch = '';
				// this.form.reset();
			},

			setForm() {
				this.form.name = this.selectedBranch.name; 
				this.form.code = this.selectedBranch.code;
				this.form.owner = this.selectedBranch.owner;
				this.form.contact = this.selectedBranch.contact;
				this.form.email = this.selectedBranch.email;
				this.form.registration_no = this.selectedBranch.registration_no;
				this.form.payment_bank = this.selectedBranch.payment_bank;
				this.form.payment_acc_no = this.selectedBranch.payment_acc_no;
				this.form.gst_no = this.selectedBranch.gst_no;
				this.form.fax = this.selectedBranch.fax;
				this.form.tollfree = this.selectedBranch.tollfree; 
				this.form.website = this.selectedBranch.website;
				this.form.address = this.selectedBranch.address;
				this.form.product_type_id = this.selectedBranch.product_type_id;
				this.form.registered_company_name = this.selectedBranch.registered_company_name;
        this.form.contact_emails = this.selectedBranch.contact_emails;
        this.form.lc_code = this.selectedBranch.lc_code;

				this.selectedType = _.filter(this.types, function(type){ return this.form.product_type_id == type.value; }.bind(this))[0];
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
				$("#branch-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedBranch ? "Edit branch - " + this.selectedBranch.name : "Create branch";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedBranch ? "Update" : "Create";
			},

			url() {
				return this.selectedBranch ? "/admin/branches/" + this.selectedBranch.id : "/admin/branches";
			}
		},

		watch: {
			selectedType(newVal, oldVal) {
				if(newVal) {
					this.form.product_type_id = newVal.value;
				}
			}
		}
	}
</script>