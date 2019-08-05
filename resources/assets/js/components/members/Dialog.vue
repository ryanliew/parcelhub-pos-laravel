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
	      		<div class="modal-body">
	        		<members-form 
	        			ref="memberForm" 
	        			:isEdit="isEdit"
	        			@success="onSuccess"
	        			@submitting="submitting = true"
	        			:url="url">
	        		</members-form>
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
		props: ['shouldShow'],

		data() {
			return {
				isActive: false,
				selectedMember: '',
				isEdit: false,
				submitting: false,
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
				this.$refs.memberForm.form.reset();
			},

			setForm() {
				this.$refs.memberForm.form.name = this.selectedMember.name;
				this.$refs.memberForm.form.email = this.selectedMember.email;
				this.$refs.memberForm.form.phone_number = this.selectedMember.phone_number;
				this.$refs.memberForm.form.address_line_1 = this.selectedMember.address_line_1;
				this.$refs.memberForm.form.address_line_2 = this.selectedMember.address_line_2;
				this.$refs.memberForm.form.city = this.selectedMember.city;
				this.$refs.memberForm.form.postcode = this.selectedMember.postcode;
				this.$refs.memberForm.form.country = this.selectedMember.country;
				this.$refs.memberForm.form.gender = this.selectedMember.gender;
				this.$refs.memberForm.form.birthdate = this.selectedMember.birthdate;
				this.$refs.memberForm.form.state = this.selectedMember.state;

				this.$refs.memberForm.selectedGender = {label: this.selectedMember.gender, value: this.selectedMember.gender};
			},

			submit() {
				this.$refs.memberForm.submit();
			},

			onSuccess(response) {
				this.$emit("memberCreated", {member: response.member});

				this.submitting = false;

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
				return this.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedMember ? "Update" : "Create";
			},

			url() {
				return this.selectedMember ? "/members/" + this.selectedMember.id : "/members";
			},
		}

	}
</script>