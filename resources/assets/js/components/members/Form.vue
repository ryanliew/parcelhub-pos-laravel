<template>
	<form @submit.prevent="submit" 
		@keydown="form.errors.clear($event.target.name)" 
		@input="form.errors.clear($event.target.name)">
		<div class="row">
			<div class="col">
				<text-input v-model="form.name" 
					:defaultValue="form.name"
					:required="true"
					type="text"
					label="First name"
					name="name"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('name')">
				</text-input>
			</div>
			<div class="col">
				<text-input v-model="form.last_name" 
					:defaultValue="form.last_name"
					:required="true"
					type="text"
					label="Last name"
					name="last_name"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('last_name')">
				</text-input>
			</div>
		</div>
		<div class="row">
			<div class="col-12 col-md-6">
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
			<div class="col-12 col-md-6">
				<text-input v-model="form.email" 
					:defaultValue="form.email"
					:required="true"
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
			<div class="col-12 col-md-6">
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
			<div class="col-12 col-md-6">
				<text-input v-model="form.birthdate" 
					:defaultValue="form.birthdate"
					:required="true"
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
			<div class="col-12 col-md-6">
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
			<div class="col-12 col-md-6">
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
			<div class="col-12 col-md-6">
				<text-input v-model="form.city" 
					:defaultValue="form.city"
					:required="true"
					type="string"
					label="City"
					name="city"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('city')">
				</text-input>
			</div>
			<div class="col-12 col-md-6">
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
			<div class="col-12 col-md-6">
				<text-input v-model="form.state" 
					:defaultValue="form.state"
					:required="true"
					type="string"
					label="State"
					name="state"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('state')">
				</text-input>
			</div>
			<div class="col-12 col-md-6">
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

		<button type="button" class="my-2 btn btn-primary" v-if="selfSubmit" @click="submit">
			Become 20% COOLER!
		</button>

		<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
	</form>
</template>

<script>
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
	export default {
		props: ['isEdit', 'url', 'confirmMessage', 'selfSubmit', 'secondaryMessage', 'shouldRedirect'],

		mixins: [ConfirmationMixin],

		mounted() {
			if(this.confirmMessage)
				this.confirm_message = this.confirmMessage;

			if(this.secondaryMessage)
				this.secondary_message = this.secondaryMessage;
		},

		data() {
			return {
				form: new Form({
					name: '',
					email: '',
					last_name: '',
					phone_number: '',
					address_line_1:'',
					address_line_2:'',
					city: '',
					postcode: '',
					country: '',
					gender: 'Male',
					birthdate: '',
					state: '',
				}),
				genders: [
						{label: 'Male', value: 'Male'},
						{label: 'Female', value: 'Female'},
						],
				selectedGender: {label: 'Male', value: 'Male'},
			};
		},

		methods: {
			submit() {
				this.isConfirming = true;
			},

			confirmSubmit() {
				this.isConfirming = false;
				this.$emit("submitting");
				this.form.post(this.url)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
				this.$emit("success", {member: response.member});

				if(this.shouldRedirect)
					window.location.href = "/members/" + response.member.id + "/success";
			},

			onError(error) {
				console.log(error);
			}
		},

		watch: {
			selectedGender(newVal, oldVal) {
				this.form.gender = newVal.value;
			}
		}	
	}
</script>