<template>
	<div class="member-page text-center">
		<template v-if="!is_new && !is_existing">
			<h2>I am...</h2>

			<div class="row">
				<div class="col-12">
					<button class="btn btn-primary" @click="is_new = true">
						Applying for membership
					</button>
				</div>
				<div class="col-12 mt-1">
					<button class="btn btn-primary" @click="is_existing = true">
						Already a member
					</button>
				</div>
			</div>
		</template>
		<members-form
			:is-edit="false"
			url="/members"
			ref="memberForm"
			confirm-message="Terms and conditions"
			secondary-message="By clicking on the OK button below I agree to the terms an conditions of Hexabox Boardgame Cafe"
			:self-submit="true"
			:should-redirect="true"
			v-if="is_new"
			>
			
		</members-form>
		<members-search :manual="true" v-if="is_existing">

		</members-search>
	</div>
</template>
<script>
	import MembersSearch from "./Search.vue";
	export default {
		components:{ MembersSearch },

		data() { 
			return {
				is_new: false,
				is_existing: false,
			};
		},

		computed: {
			is_main() {
				return !(this.is_new || this.is_existing);
			}
		}
	}
</script>