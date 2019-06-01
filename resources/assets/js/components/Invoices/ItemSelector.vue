<template>
	<div class="items-selector d-flex flex-column">
		<carousel class="categories py-3 border-bottom"
			:perPage="4"
			:navigationEnabled="true"
			:paginationEnabled="false">
			<slide class="px-2" v-for="category in categories" :key="category.id" v-if="category.products.length > 0">
				<button class="btn btn-primary btn-block" @click="setCategory(category)">
					{{ category.name }}
				</button>
			</slide>
		</carousel>
		<div class="items py-3 flex-grow-1">
			<div class="row no-gutters">
				<div class="col-md-4" v-for="product in products">
					<button class="btn btn-primary btn-block text-break" @click="selectItem(product)">
						{{ product.sku }} <br>
						<small>{{ product.description }}</small>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import { Carousel, Slide } from 'vue-carousel';

	export default {
		props: [''],

		components: {
			Carousel, 
			Slide
		},

		data() {
			return {
				categories: [],
				selectedCategory: '',
				products: [],
			};
		},

		mounted() {
			this.getCategories();
		},

		methods: {
			getCategories(error = "", tries = 0) {
				axios.get("/data/producttypes")
					.then(response => this.setCategories(response))
					.catch(error => this.getCategories(error, ++tries));
			},

			setCategories(response) {
				this.categories = response.data;
				this.selectedCategory = this.setCategory(response.data[0]);
			},

			setCategory(category) {
				this.selectedCategory = category;
				this.products = _.sortBy(category.products, [function(item){ return item.sku; }]);
			},

			selectItem(product) {
				this.$emit('selected', {item: product});
			}
		}	
	}
</script>