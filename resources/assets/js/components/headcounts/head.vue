<template>
	<zoom-x-transition :delay="transitionDelay">
		<div class="col-4 col-md-3 mt-3">
			<div class="hexatable d-flex flex-column" :class="extraClasses" @click="select">
				<div class="date text-center">
					{{ gamingTime }}
				</div>
				<div class="hexatable-name">
					{{ head.number }}
				</div>
				<div class="items" v-if="foodCount + beverageCount">
					<template v-if="beverageCount"><i class="fas fa-beer"></i> {{ beverageCount }}</template> 
					<template v-if="foodCount"><i class="fas fa-hamburger"></i> {{ foodCount }}</template>
				</div>
			</div>
		</div>
	</zoom-x-transition>
</template>
<script>
	import moment from "moment";
	import { ZoomXTransition } from 'vue2-transitions'
	export default {
		props: ['manualClass', 'head', 'index'],

		components: {
			ZoomXTransition,
		},

		data() { 
			return {
				selected: false,
				now: moment()
			};
		},

		mounted() {
			this.startAndRefreshTimer();
		},

		methods: {
			select() {
				this.selected = !this.selected;
				this.$emit('selected');
			},

			startAndRefreshTimer() {
		      this.timerInterval = setInterval(() => {
		        this.now = moment();
		      }, 1000);
		    }
		},

		computed: {
			extraClasses() {
				return [this.selected ? "selected" : ""];
			},

			beverageCount() {
				return _.sumBy(_.filter(this.head.active_session.items, (item) => { return item.product_type_id == 1; }), (item) => { return item.unit; });
			},

			foodCount() {
				return _.sumBy(_.filter(this.head.active_session.items, (item) => { return item.product_type_id == 3; }), (item) => { return item.unit; });
			},

			gamingTime() {
		        var duration = moment.duration(
		          moment(this.now).diff(this.head.activated_at)
		        );

		        var hours = duration.hours();
		        var minutes = duration.minutes();
		        var seconds = duration.seconds();

		        return (
		          hours.toString().padStart(2, "0") +
		          ":" +
		          minutes.toString().padStart(2, "0") +
		          ":" +
		          seconds.toString().padStart(2, "0")
		        );
		    },

		    transitionDelay() {
		    	return parseInt(this.index) * 100;
		    }
		}
	}
</script>