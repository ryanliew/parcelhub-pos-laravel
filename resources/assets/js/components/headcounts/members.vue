<template>
	<div class="members-adder text-center">
		<h2>Members adder</h2>
		<div class="members-list">
			<div class="d-flex align-items-center pt-1 px-2" v-for="(member, index) in members">
				<img src="/img/favicon.png" width="30px" class="mr-2">
				<div class="flex-grow-1 text-left">{{ member.name }}</div>
				<i class="fas fa-trash text-danger" @click="sub(index)"></i>
			</div>
		</div>
		<scale-transition>
			<template v-if="is_scanning">
				<qrcode-stream @decode="addQrcode"></qrcode-stream>
			</template>
		</scale-transition>
		<input class="form-control my-3" placeholder="Member ID / Phone number" v-model="member_id" @key.enter="add"/>
		<button class="btn btn-success" @click="is_scanning = true">Scan QR code</button>
		<button class="btn btn-success" @click="add">Add</button>
		<button class="btn btn-secondary" @click="close">Complete</button>
	</div>
</template>

<script>

	export default {
		props: ['members'],

		data() {
			return {
				member_id: "",
				is_scanning: false,
			};
		},

		methods: {
			add() {
				axios.get("/members/search?keyword=" + this.member_id)
					.then(respond => this.setMember(respond))
					.catch(error => this.onError(error));
			},

			addQrcode(qrcode) {
				axios.get("/members/search?keyword=" + qrcode)
					.then(respond => this.setMember(respond))
					.catch(error => this.onError(error));
			},


			sub(index) {
				this.$emit('sub', index);
			},

			setMember(respond) {
				this.is_scanning = false;

				if(respond.data && !respond.data[0]) flash("Member not found");

				else if(!respond.data[0].is_active) flash("Member expired on " + respond.data[0].expire_date);
				
				else { this.$emit('add', respond.data[0]); this.member_id = ''; }
			},

			onError(error) {
				console.log(error);
			},

			close() {
				this.$emit('close');
			}
		}
	}
</script>