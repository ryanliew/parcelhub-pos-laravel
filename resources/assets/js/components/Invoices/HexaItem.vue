<template>
	<tr>
		<td class="item-name">{{ item.sku }}<br><small class="display-mobile">{{ item.description }}</small></td>
		<td class="display-desktop"><small><i>{{ item.description }}</i></small></td>
		<td>{{ unitPrice.toFixed(2) }}</td>
		<td>{{ item.unit }}</td>
		<!-- <td>{{ taxValue.toFixed(2) }}</td> -->
		<td>{{ totalPrice.toFixed(2) }}</td>

		<td v-if="canDelete"><a href="#"><i class="fas fa-trash-alt text-danger" @click.prevent="deleteItem"></i></a></td>
	</tr>
</template>

<script>
	export default {
		props: ['item', 'canDelete'],
		data() {
			return {

			};
		},

		methods: {
			deleteItem() {
				this.$emit('delete');
			},
		},

		computed: {
			unitPrice() {
				return parseFloat(this.item.price);
			},

			taxValue() {
				return this.item.tax_value ? this.item.tax_value : this.item.tax;
			},

			totalPrice() {
				return this.item.total ? this.item.total : this.item.total_price;
			}
		}
	}
</script>