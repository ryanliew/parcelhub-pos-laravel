<template>
	<div class="fullscreen-page">
		<v-collapse-group>
			<v-collapse-wrapper v-for="head in heads" :key="head.id">
				<div class="head-info row" v-collapse-toggle>
					<div class="col-2 number">
						#{{ head.number }}
					</div>
					<div class="col-7 time">
						{{ head.activated_at }}
					</div>
					<div class="col-3 subtotal">
						RM{{ calculateHeadTotal(head) | price }}
					</div>
				</div>
				<div v-collapse-content>
					<div class="hexaitem row" v-for="item in head.active_session.items" :key="item.id">
						<div class="col-9 time">
							{{ item.description }} x <b>{{ item.unit }}</b>
						</div>
						<div class="col-3 subtotal">
							RM{{ item.total_price | price }}
						</div>
					</div>
				</div>
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
				@close="isAddingMember = false"
				v-if="isAddingMember">

			</members-adder>
		</scale-transition>
	</div>
</template>

<script>
	import membersAdder from "./members.vue";

	export default {
		props: ['heads'],

		components: {
			membersAdder
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
				selectedGaming: {'label': 'Gaming Type: Auto', 'key': 0},
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

			calculateHeadTotal(head) {
				return _.sumBy(head.active_session.items, (item) => { return item.total_price; });
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
		}	
	}
</script>