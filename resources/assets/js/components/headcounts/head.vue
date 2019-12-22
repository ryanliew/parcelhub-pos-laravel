<template>
	<div class="hexatable" :class="extraClasses" @click="select">
		<div class="date">
			{{ head.date }}
		</div>
		<div class="number">
			{{ head.number }}
		</div>
		<div class="items" v-if="this.foodCount + this.beverageCount">
			{{ beverage_count }} {{ food_count }}
		</div>
	</div>
</template>
<script>
	export default {
		props: ['manualClass', 'head', 'headId'],

		data() { 
			return {
				selected: false,
			};
		},

		mounted() {
			this.selected = this.head.is_active;
		},

		methods: {
			select() {
				this.selected = !this.selected;
				this.$emit( this.selected ? : 'deselected' : 'selected');
			},
		},

		computed: {
			extraClasses() {
				return this.manualClass;
			},

			beverageCount() {
				return 0;
			},

			foodCount() {
				return 0
			},
		}
	}
</script>