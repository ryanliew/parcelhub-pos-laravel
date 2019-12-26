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

		<scale-transition>
			<div class="members-adder" v-if="isBilling">
				
				<selector-input :potentialData="paymentMethods"
					v-model="selectedPaymentMethod" 
					:defaultData="selectedPaymentMethod"
					placeholder="Select payment method"
					:required="true"
					label="Payment method"
					name="payment_method"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('payment_method')">
				</selector-input>

				<div class="row border-top border-bottom py-2 my-2">
					<div class="col-9">
						<b>Subtotal</b>
					</div>
					<div class="col-3">
						<b>RM{{ form.subtotal | price }}</b>
					</div>
				</div>

				<text-input v-model="form.discount" 
					:defaultValue="form.discount"
					:required="false"
					type="number"
					step="0.01"
					label="Discount"
					name="discount"
					:editable="true"
					:focus="true"
					:hideLabel="false"
					:error="form.errors.get('discount')">
				</text-input>

				<div class="row border-top border-bottom py-2 my-2">
					<div class="col-9">
						<b>Total</b>
					</div>
					<div class="col-3">
						<b>RM{{ total | price }}</b>
					</div>
				</div>

				<text-input v-model="form.paid" 
					:defaultValue="form.paid"
					:required="true"
					type="number"
					step="0.01"
					label="Paid"
					name="paid"
					:editable="true"
					:focus="false"
					:hideLabel="false"
					:error="form.errors.get('paid')">
				</text-input>

				<div class="row border-top border-bottom py-2 my-2">
					<div class="col-9">
						<b>Change</b>
					</div>
					<div class="col-3">
						<b>RM{{ change | price }}</b>
					</div>
				</div>

				<button class="btn btn-block btn-success" @click="submitBilling">
					Confirm
				</button>
				<button class="btn btn-block btn-secondary" @click="cancelBilling">
					Cancel
				</button>
			</div>
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
					discount: 0,
					remarks: '',
					paid: 0,
					total: 0,
					subtotal: 0,
					payment_method: 'Cash'
				}),
				isBilling: false,
				gamings: [],
				originalGaming: [],
				selectedGaming: {'label': 'Gaming Type: Select a gaming type', 'key': 0},
				paymentMethods: [
					{ label: 'GrabPay', value: 'GrabPay' },
					{ label: 'Fave', value: 'Fave' },
					{ label: 'Cash', value: 'Cash' },
					{ label: 'MBB QR Pay', value: 'MBB QR Pay' },
					{ label: 'DuitNow', value: 'DuitNow' },
					{ label: 'Bank Transfer', value: 'Bank Transfer' }
				],
				selectedPaymentMethod: { label: 'Cash', value: 'Cash' },
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

				this.form.subtotal = _.sumBy(this.form.heads, (head) => { return this.calculateHeadTotal(head); });

				this.isBilling = true;
				
			},

			calculateHeadTotal(head) {
				let isMember =  head.active_session.member_id ;
				let items_price = _.sumBy(head.active_session.items, (item) => { 
											return isMember
													? this.calculateItemMemberTotalPrice(item) 
													: this.calculateItemTotalPrice(item); 
												});
				let gaming_price = isMember 
					? this.calculateItemMemberTotalPrice(head.gaming_item) 
					: this.calculateItemTotalPrice(head.gaming_item);

				return gaming_price + items_price;
			},

			cancelBilling() {
				this.isBilling = false;
				this.form.discount = 0;
				this.form.paid = 0;
			},

			submitBilling() {

				this.form.heads.forEach((head) => {
					Vue.set(head, 'total_price', this.calculateHeadTotal(head));
				});
				this.form.total = this.total;
				this.form.change = this.change;

				this.form.post("/bill")
					.then(response => this.onBillSuccess(response))
					.catch(error => this.catchAjaxError(error));
			},

			onBillSuccess(response) {
				window.location.reload();
			}
		},

		computed: {
			total() {
				return Math.max(0, this.form.subtotal - parseFloat(this.form.discount));
			},

			change() {
				return Math.max(parseFloat(this.form.paid) - this.total, 0);
			},
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
			},

			selectedPaymentMethod(newVal) {
				this.form.selectedPaymentMethod(newVal.value);
			}
		}	
	}
</script>