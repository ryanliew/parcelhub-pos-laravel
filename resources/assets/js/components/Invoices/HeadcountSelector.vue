<template>
	<modal
		id="headcount-selector"
		:active="show"
		size="modal-full"
		@close="close"
		@open="open">

		<template slot="header">
			Select headcounts
		</template>

		<div class="row border-bottom mb-3">
			<div class="col-md-2 my-3" v-for="head in availableHead" v-if="currentFilter == 'inactive'">
				<button class="btn btn-secondary btn-block btn-lg text-big" @click="selectInactiveHead(head)">
					{{ head.number }}
				</button>
			</div>
			<div class="col-md-2 my-3" v-for="head in activeHead" v-if="currentFilter == 'active'">
				<button class="btn btn-success btn-block btn-lg text-big" @click="selectActiveHead(head)">
					{{ head.number }}
				</button>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-2 my-3" v-for="head in selectedHead">
				<button class="btn btn-success btn-block btn-lg text-big">
					{{ head.number }}
				</button>
			</div>
		</div>



		<template slot="footer">
			<button class="btn btn-secondary" @click="close">Cancel</button>
			<button class="btn btn-primary" @click="commitSelection" @disabled="selectedHead.length < 1">Select</button>
		</template>
	</modal>
</template>

<script>
	import Modal from "../Modal.vue";
	export default {
		props: ['table', 'show', 'currentFilter'],

		components: {
			Modal,
		},

		data() {
			return {
				availableHead: [],
				activeHead: [],
				selectedHead: [],
			};
		},

		mounted() {
			this.getHeads();
		},

		methods: {
			open() {
				this.getHeads();
			},

			getHeads(error = '', tries = 0) {
				if(tries < 3)
					axios.get("/data/heads")
						.then(response => this.setHeads(response))
						.catch(error => this.getHeads(error, ++tries));
			},

			setHeads(response) {
				let heads = response.data;
				this.availableHead = _.filter(heads, function(head){ return !head.is_active; });
				this.activeHead = _.filter(heads, function(head){ return head.is_active; });
			},

			close() {
				this.selectedHead = [];
				this.$emit('close');
			},

			selectInactiveHead(headOption) {
				if(_.findIndex(this.selectedHead, function(head){ return head.is_active; }) >= 0)
					this.selectedHead = [];

				this.selectHead(headOption);
			},

			selectActiveHead(headOption) {
				if(_.findIndex(this.selectedHead, function(head){ return !head.is_active; }) >= 0)
					this.selectedHead = [];

				this.selectHead(headOption);
			},

			selectHead(headOption) {
				if(_.findIndex(this.selectedHead, function(head){ return head.number == headOption.number}.bind(headOption)) < 0)
					this.selectedHead.push(headOption);
			},

			commitSelection() {
				this.$emit("confirm", {heads: this.selectedHead});
			}
		}	
	}
</script>