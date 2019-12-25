<template>
	<div class="fullscreen-page">
		<v-collapse-group>
			<v-collapse-wrapper v-for="head in form.heads" :key="head.id">
				<bill-head :head="head">

				</bill-head>
			</v-collapse-wrapper>
		</v-collapse-group>

		<slide-y-down-transition :duration="200">
			<div class="bottom-menu" v-show="showMenu">
				<div class="row mb-3 p-2 pt-0 border-bottom">
					<button class="btn btn-primary btn-block" @click="showGamingSelector = true">{{ selectedGaming.label }}</button>
					<v-cascade
						v-model="showGamingSelector"
						:data="gamings"
						:title="['Select gaming type']"
						:filterable="true"
						@success="setGamingType">
					</v-cascade>
				</div>
				<button class="btn btn-success btn-block" @click="isAddingMember = true">Member ({{ form.members.length }})</button>
				<button class="btn btn-info btn-block" @click="confirmBilling">Confirm and calculate total</button>
				<button class="btn btn-secondary btn-block" @click="close">Cancel</button>
			</div>
		</slide-y-down-transition>

		<scale-transition>
			<members-adder 
				:members="form.members" 
				@add="addMember" 
				@sub="subMember"
				@close="calculateMember"
				v-if="isAddingMember">

			</members-adder>
		</scale-transition>
	</div>
</template>

<script>
	import membersAdder from "./members.vue";
	import billHead from "./BillHead.vue"
	export default {
		props: ['heads'],

		components: {
			membersAdder,
			billHead,
		},

		data() {
			return {
				showMenu: false,
				showGamingSelector: false,
				isAddingMember: false,
				form: new Form({
					gaming_id: 0,
					members: [],
					heads: [],
				}),
				gamings: [],
				originalGaming: [],
				selectedGaming: {'label': 'Gaming Type: Select a gaming type', 'key': 0},
			};
		},

		mounted() {
			setTimeout(function(){ this.showMenu = true; }.bind(this), 500);
			this.setBillingHeads();
			this.getGamingProducts();
		},


		methods: {
			setBillingHeads() {
				this.form.heads = this.heads;
			},

			getGamingProducts() {
				axios.get("/data/products/type/4")
					.then(response => this.setGamingProducts(response))
					.catch(error => this.catchAjaxError(error));
			},

			setGamingProducts(response) {
				this.originalGaming = response.data;
				this.gamings = response.data.map((product) => {
					let obj = {};

					obj['key'] = product.id;
					obj['label'] = product.description;

					return obj;
				});
			},

			setGamingType(e) {
				this.selectedGaming = {'key': e.path[0], 'label': "Gaming type: " + e.pathName[0]};
				this.form.gaming_id = e.path[0];
			},

			addMember(e) {
				let existing = _.findIndex(this.form.members, function(member){ return e.id == member.id; }.bind(e));

				if(existing == -1) {
					this.form.members.push(e);
				}
			},

			subMember(e) {
				this.form.members.splice(e, 1);
			},

			calculateMember() {
				// Reset all members data
				this.form.heads.forEach((head) => { head.active_session.member_id = null; });

				this.form.members.forEach((member) => {
					let selectedHead = _.reverse(
											_.sortBy(
												_.filter(this.form.heads, 
															(head) => { 
																return !head.active_session.member_id; 
															}), 
												(head) => {
													return _.sumBy(head.active_session.items, (item) => {
														return item.total_price;
													}) - _.sumBy(head.active_session.items, (item) => {
														return item.member_price * item.unit;
													}) + head.gaming_item.total - head.gaming_item.member_price;

												})
											);

					selectedHead[0].active_session.member_id = member.id;
				});
				
				this.isAddingMember = false;
			},

			close() {
				this.form.reset();
				this.$emit('close');
			},

			confirmBilling() {
				this.form.post("/bill", false)
						.then(response => this.onBillSuccess(response))
						.catch(error => this.catchAjaxError(error));
			},

			onBillSuccess(response) {
				console.log(response);
			}
		},

		watch: {
			selectedGaming(newVal) {
				// Try to find the gaming item from the original list
				let gaming = _.filter(this.originalGaming, (product) => { return product.id == newVal.key; })[0];

				if(gaming) {
					this.form.heads.forEach((head) => {
						Vue.set(gaming, 'unit', 1);
						Vue.set(gaming, 'tax_value', this.calculateItemTax(gaming));
						Vue.set(gaming, 'total', this.calculateItemTotalPrice(gaming));
						Vue.set(gaming, 'member_total', this.calculateItemMemberTotalPrice(gaming));
						Vue.set(head, 'gaming_item', gaming);
					});
				}
			}
		}	
	}
</script>