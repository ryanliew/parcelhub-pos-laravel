<template>
	<div class="hexaitems">
		<div class="head-info row">
			<div class="col-2 number">
				#{{ head.number }}
			</div>
			<div class="col-6 time">
				{{ head.activated_at }}
			</div>
			<div class="col-4 subtotal">
				RM{{ subtotal | price }}
			</div>
		</div>
		<div class="hexaitem row add" @click="addItem">
			<div class="col-12 text-center">
				Add Item
			</div>
		</div>
		<div class="hexaitem row" v-for="(item, index) in form.items">
			<div class="col-8 time">
				{{ item.description }} x <b>{{ item.unit }}</b>
			</div>
			<div class="col-3 subtotal">
				RM{{ item.total | price }}
			</div>
			<div class="col-1">
				<i class="fas fa-trash-alt text-danger" @click="deleteItem(index)"></i>
			</div>
		</div>
		<div class="hexaitem row confirmed" v-for="item in current_session.items">
			<div class="col-8 time">
				{{ item.description }} x <b>{{ item.unit }}</b>
			</div>
			<div class="col-3 subtotal">
				RM{{ calculateItemTotalPrice(item) | price }}
			</div>
			<div class="col-1">
				<i class="fas fa-trash-alt text-danger" @click="deleteInvoiceItem(item)"></i>
			</div>
		</div>

		<slide-y-down-transition :duration="200">
			<div class="bottom-menu" v-show="showMenu">
				<div class="row mb-3 pb-2 border-bottom">
					<div class="col-9 text-right">
						<b>Total to be added: </b>
					</div>
					<div class="col-3 text-right">
						RM {{ pendingSubtotal | price }}
					</div>
				</div>
				<button class="btn btn-success btn-block" @click="confirmOrder">Confirm</button>
				<button class="btn btn-info btn-block" @click="close">Close</button>
			</div>
		</slide-y-down-transition>

		<modal :active="isAddingItem"
			id="items-selector"
			@close="isAddingItem = false">
			<item-selector
				@selected="selectItem"
				key="2"
			>

			<template slot="header">
				Select item
			</template>

			</item-selector>
		</modal>
	</div>
</template>

<script>
	import ItemSelector from "./ItemSelector.vue";

	export default {
		props: ['head'],

		components: {
			ItemSelector,
		},

		data() {
			return {
				showMenu: false,
				form: new Form({
					items: [],
				}),
				isAddingItem: false,
				isCreatingItem: false,
				categories: [],
				current_session: this.head.active_session
			};
		},

		mounted() {
			setTimeout(function(){ this.showMenu = true; }.bind(this), 500);
		},

		methods: {
			addItem() {
				this.isAddingItem = true;
			},

			selectItem(e) {
				let selectedItem = e.item ? e.item : e;

				let existing = _.findIndex(this.form.items, function(item){ return selectedItem.description == item.description; }.bind(selectedItem));

				if(existing > -1) {
					this.form.items[existing].unit++;
					this.form.items[existing].tax_value = this.calculateItemTax(this.form.items[existing]);
					this.form.items[existing].total = this.calculateItemTotalPrice(this.form.items[existing]);
				} else {
					Vue.set(selectedItem, 'unit', 1);
					Vue.set(selectedItem, 'tax_value', this.calculateItemTax(selectedItem));
					Vue.set(selectedItem, 'total', this.calculateItemTotalPrice(selectedItem));
					this.form.items.push(selectedItem);
				}
			},

			deleteItem(index) {
				this.form.items.splice(index, 1);
			},

			deleteInvoiceItem(id) {
				axios.post("/items/destroy/" + id)
					.then(response => this.deleteInvoiceItemSuccess(response))
					.catch(error => this.handleError(error));
			},

			calculateItemTax(item) {
				let tax = 0;

				if(item.is_tax_inclusive) {
					tax = item.price - (Math.round(parseFloat(item.price) / (item.tax.percentage / 100 + 1) * 100) / 100 );
				}
				else {
					tax = Math.round(parseFloat(item.price) * item.tax_rate) / 100;
				}

				return tax * item.unit;
			},

			calculateItemTotalPrice(item) {
				let total = parseFloat(item.price) * item.unit;
				
				if(!item.is_tax_inclusive)
					total += this.calculateItemTax(item);

				return total;
			},

			confirmOrder() {
				this.form.post("/heads/" + this.head.id + "/order")
					.then(response => this.onSuccess(response))
					.catch(error => this.catchAjaxError(error));
			},

			onSuccess(response) {
				this.current_session = response.session;
				this.form.items = [];
			},

			close() {
				this.$emit("close");
			}
		},

		computed: {
			pendingSubtotal() {
				return _.sumBy(this.form.items, (item) => { return item.total });
			},

			subtotal() {
				return _.sumBy(this.current_session.items, (item) => { return this.calculateItemTotalPrice(item); });
			}
		}	
	}
</script>