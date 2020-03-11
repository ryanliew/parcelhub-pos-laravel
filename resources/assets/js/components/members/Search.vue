<template>
	<div class="relative">
		<text-input v-model="phone" 
			:defaultValue="phone"
			:required="false"
			type="text"
			label="Member"
			name="member"
			:editable="true"
			:focus="true"
			:hideLabel="true"
			@enter="search"
			placeholder="Member ID / Phone number">
		</text-input>

		<button class="btn btn-primary" v-if="manual" @click="search">Search</button>

		<div class="d-flex align-items-center member-info pt-1 px-2" v-if="member" @click="member = ''">
			<img src="/img/favicon.png" width="30px" class="mr-2">{{ member.name }}
		</div>
	</div>
</template>

<script>
	export default {
		props: ['manual'],
		data() {
			return {
				member: '',
				phone: '',
				members: [],
			};
		},

		methods: {
			search() {
				axios.get("/members/search?keyword=" + this.phone)
					.then(respond => this.setMember(respond))
					.catch(error => this.onError(error));
			},

			setMember(respond) {
				this.members = respond.data;

				if(this.manual && respond.data[0])
					window.location.href = "/members/" + respond.data[0].id + "/success";
				else
					flash("Sorry we are unable to find your details, please contact our staff for assistance", "danger");

				if(this.members.length == 1)
					this.member = this.members[0];
			},

			onError(error) {
				console.log(error);
			}
		},

		watch: {
			member(newVal) {
				this.$emit('member', newVal);
			}
		}	
	}
</script>