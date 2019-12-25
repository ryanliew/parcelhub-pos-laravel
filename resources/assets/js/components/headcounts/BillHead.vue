<template>
	<div>
		<div class="head-info row" v-collapse-toggle>
			<div class="col-2 number">
				#{{ head.number }} <img src="/img/favicon.png" width="30px" class="ml-2" v-if="head.active_session.member_id">
			</div>
			<div class="col-7 time">
				{{ head.activated_at }} ({{ calculateDuration(head) }})
			</div>
			<div class="col-3 subtotal">
				RM{{ headTotal | price }}
			</div>
		</div>
		<div v-collapse-content>
			<div class="hexaitem row" v-for="item in head.active_session.items" :key="item.id">
				<div class="col-9 time">
					{{ item.description }} x <b>{{ item.unit }}</b>
				</div>
				<div class="col-3 subtotal" v-if="!head.active_session.member_id">
					RM{{ item.total_price | price }}
				</div>
				<div class="col-3 subtotal" v-else>
					RM{{ item.member_price * item.unit | price }}
				</div>
			</div>
			<div class="hexaitem row" v-if="head.gaming_item">
				<div class="col-9 time">
					{{ head.gaming_item.description }} x <b>{{ head.gaming_item.unit }}</b>
				</div>
				<div class="col-3 subtotal" v-if="!head.active_session.member_id">
					RM{{ head.gaming_item.total | price }}
				</div>
				<div class="col-3 subtotal" v-else>
					RM{{ head.gaming_item.member_total | price }}
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import moment from "moment";
	export default {
		props: ['head'],

		data() { 
			return {
				now: moment()
			};
		},

		methods: {
			calculateDuration(head) {
				var duration = moment.duration(
		          moment(this.now).diff(head.activated_at)
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
			}
		},

		computed: {
			headTotal() {
				let gaming_price = 0;
				let is_member = this.head.active_session.member_id;

				if(this.head.gaming_item) {
					gaming_price = is_member ? this.head.gaming_item.member_total : this.head.gaming_item.total
				}

				let itemTotal = _.sumBy(this.head.active_session.items, (item) => { return is_member ? item.member_price * item.unit : item.total_price; });

				return  itemTotal + gaming_price;
			},
		}
	}
</script>