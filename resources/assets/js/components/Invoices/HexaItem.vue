<template>
	<tr>
		<td>{{ item.sku }}</td>
		<td><small><i>{{ item.description }}</i></small></td>
		<td>{{ item.price }}</td>
		<td>{{ item.unit }}</td>
		<td>{{ item.tax_value.toFixed(2) }}</td>
		<td>{{ item.total.toFixed(2) }}</td>

		<td><a href="#"><i class="fas fa-trash-alt text-danger" @click.prevent="deleteItem"></i></a></td>
	</tr>
</template>

<script>
	export default {
		props: ['item'],
		data() {
			return {

			};
		},

		methods: {
			deleteItem() {
				this.$emit('delete');
			},
		},
	}
</script>