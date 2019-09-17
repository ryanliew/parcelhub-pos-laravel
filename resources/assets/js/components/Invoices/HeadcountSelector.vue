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

		<div class="row">
			<div class="col-md-8 headcount-scroller">
				<div class="row border-right mb-3 border-bottom">
					<div class="col-4 col-md-3 my-1" v-for="head in availableHead" v-if="currentFilter == 'inactive'">
						<button class="btn btn-secondary btn-block btn-lg text-big" @click="selectInactiveHead(head)">
							{{ head.number }}
						</button>
					</div>
					<div class="col-4 col-md-2 my-1" v-for="head in activeHead" v-if="currentFilter == 'active'">
						<button class="btn btn-success btn-block btn-lg text-big" @click="selectActiveHead(head)">
							{{ head.number }}
						</button>
					</div>
				</div>
			</div>
			<div class="col-md-4 headcount-scroller">
				<div class="row mt-3">
					<div class="col-4 col-md-6 my-1" v-for="(head, index) in selectedHead">
						<button class="btn btn-success btn-block btn-lg text-big" @click="deselectHead(index)">
							{{ head.number }}
						</button>

						<member-search v-if="head.is_active" class="mt-2" @member="setMember($event, index)"></member-search>
					</div>
				</div>
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
	import MemberSearch from "../members/Search.vue";

	export default {
		props: ['table', 'show', 'currentFilter'],

		components: {
			Modal,
			MemberSearch,
		},

		data() {
			return {
				availableHead: [],
				activeHead: [],
				selectedHead: [],
				phones: []
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
					this.selectedHead.unshift(headOption);
			},

			commitSelection() {
				this.$emit("confirm", {heads: this.selectedHead});
			},

			deselectHead(index) {
				this.selectedHead.splice(index, 1);
			},

			setMember(event, index) {
				this.selectedHead[index].member = event;
			}
		},	
	}
</script>