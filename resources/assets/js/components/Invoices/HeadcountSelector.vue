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

		<div class="products-locking" v-if="currentFilter == 'active'">
			<selector-input :potentialData="potentialHours"
				v-model="selectedHour" 
				:defaultData="selectedHour"
				placeholder="Select locked hours"
				:required="false"
				label="Hours"
				name="hours"
				:editable="true"
				:focus="false"
				:hideLabel="true">
			</selector-input>
		</div>

		<div class="row">
			<div class="col-md-8 headcount-scroller border-bottom border-info py-1">
				<div class="row border-right mb-3">
					<div class="col-6 col-md-3 my-1" v-for="head in availableHead" v-if="currentFilter == 'inactive'">
						<button class="btn btn-secondary btn-block btn-lg text-big" @click="selectInactiveHead(head)">
							{{ head.number }}
						</button>
					</div>
					<div class="col-6 col-md-3 my-1" v-for="head in activeHead" v-if="currentFilter == 'active'">
						<button class="btn btn-success btn-block btn-lg text-big" @click="selectActiveHead(head)">
							{{ head.number }}<br>
							<small>{{ head.activated_at | shortDate}}</small>
						</button>
					</div>
				</div>
			</div>
			<div class="col-md-4 headcount-scroller py-1">
				<div class="row mt-3">
					<div class="col-6 col-md-6 my-1" v-for="(head, index) in selectedHead">
						<button class="btn btn-success btn-block btn-lg text-big" @click="deselectHead(index)">
							{{ head.number }}
							<template v-if="head.product_name">
								<br>
								<small>{{ head.product_name }}</small>
							</template>
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
	import moment from "moment";

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
				phones: [],
				selectedHour: '',
				potentialHours: [],
			};
		},

		filters: {
			shortDate(value) {
				return moment(value).format("D/M H:mm")
			}
		},

		mounted() {
			this.getHeads();
		},

		methods: {
			open() {
				this.getHeads();
			},

			getHeads(error = '', tries = 0) {
				if(tries < 3) {
					axios.get("/data/heads")
						.then(response => this.setHeads(response))
						.catch(error => this.getHeads(error, ++tries));
				}

				this.getHours();
			},

			setHeads(response) {
				let heads = response.data;
				this.availableHead = _.filter(heads, function(head){ return !head.is_active; });
				this.activeHead = _.filter(heads, function(head){ return head.is_active; });
			},

			getHours(error = '', tries = 0) {
				if(tries < 3) {
					axios.get("/data/hours")
						.then(response => this.setHours(response))
						.catch(error => this.getHours(error, ++tries));
				}
			},

			setHours(response) {
				this.potentialHours = response.data.map(function(hour){
					let obj = {};

					obj['label'] = hour.description;
					obj['value'] = hour.id;

					return obj;
				});
			},

			close() {
				this.selectedHead = [];
				this.selectedHour = '';
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
				if(this.selectedHour) {
					headOption.product_id = this.selectedHour.value;
					headOption.product_name = this.selectedHour.label;
				}

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