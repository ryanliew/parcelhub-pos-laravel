<template>
	<div class="modal fade" id="member-dialog" tabindex="-1" role="dialog">
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
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.phone_number" 
									:defaultValue="form.phone_number"
									:required="true"
									type="string"
									label="Phone number"
									name="phone_number"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('phone_number')">
								</text-input>
							</div>
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
						</div>	
						<div class="row">
							<div class="col">
								<selector-input :potentialData="genders"
									v-model="selectedGender" 
									:defaultData="selectedGender"
									placeholder="Select gender"
									:required="true"
									label="Gender"
									name="gender"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('gender')"
									ref="genders">
								</selector-input>
							</div>
							<div class="col">
								<text-input v-model="form.birthdate" 
									:defaultValue="form.birthdate"
									:required="false"
									type="date"
									label="Birth date"
									name="birthdate"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('birthdate')">
								</text-input>
							</div>
						</div>	
							
						<div class="row">
							<div class="col">
								<text-input v-model="form.address_line_1" 
									:defaultValue="form.address_line_1"
									:required="false"
									type="string"
									label="Address line 1"
									name="address_line_1"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('address_line_1')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.address_line_2" 
									:defaultValue="form.address_line_2"
									:required="false"
									type="string"
									label="Address line 2"
									name="address_line_2"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('address_line_2')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.city" 
									:defaultValue="form.city"
									:required="false"
									type="string"
									label="City"
									name="city"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('city')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.postcode" 
									:defaultValue="form.postcode"
									:required="false"
									type="string"
									label="Postcode"
									name="postcode"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('postcode')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.state" 
									:defaultValue="form.state"
									:required="false"
									type="string"
									label="State"
									name="state"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('state')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.country" 
									:defaultValue="form.country"
									:required="false"
									type="string"
									label="Country"
									name="country"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('country')">
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
				selectedMember: '',
				isEdit: false,
				form: new Form({
					name: '',
					email: '',
					phone_number: '',
					address_line_1:'',
					address_line_2:'',
					city: '',
					postcode: '',
					country: '',
					gender: 'Male',
					birthdate: '',
				}),
				genders: [
						{label: 'Male', value: 'Male'},
						{label: 'Female', value: 'Female'},
						],
				selectedGender: {label: 'Male', value: 'Male'},
			};
		},

		mounted() {

			window.events.$on('createMember', evt => this.createMember(evt));
			window.events.$on('editMember', evt => this.editMember(evt));

			$("#member-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},
 
		methods: {
			createMember(evt) {
				this.openDialog();
			},

			editMember(evt) {
				this.selectedMember= evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#member-dialog").modal();
				this.isActive = true;
				
			},

			closeDialog() {
				this.isActive = false;
				this.selectedMember = '';
				this.form.reset();
			},

			setForm() {
				this.form.name = this.selectedMember.name;
				this.form.email = this.selectedMember.email;
				this.form.phone_number = this.selectedMember.phone_number;
				this.form.address_line_1 = this.selectedMember.address_line_1;
				this.form.address_line_2 = this.selectedMember.address_line_2;
				this.form.city = this.selectedMember.city;
				this.form.postcode = this.selectedMember.postcode;
				this.form.country = this.selectedMember.country;
				this.form.gender = this.selectedMember.gender;
				this.form.birthdate = this.selectedMember.birthdate;

				this.selectedGender = {label: this.selectedMember.gender, value: this.selectedMember.gender};
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
				this.$emit("memberCreated", {member: response.member});

				$("#member-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			},
		},

		computed: {
			title() {
				return this.selectedMember ? "Edit member - " + this.selectedMember.name : "Create member";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedMember ? "Update" : "Create";
			},

			url() {
				return this.selectedMember ? "/members/" + this.selectedMember.id : "/members";
			},
		},

		watch: {
			selectedGender(newVal, oldVal) {
				this.form.gender = newVal.value;
			}
		}

	}
</script>