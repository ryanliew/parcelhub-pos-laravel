<template>
	<div>
		<div class="card" id="customer-type-statement-header">
			<div class="card-body"> 
				<div class="row">
					<div class="col">
						<text-input v-model="form.date_from" 
							:defaultValue="form.date_from"
							:required="true"
							type="date"
							label="Date from:"
							name="date_from"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('date_from')">
						</text-input>
					</div>
					<div class="col">
						<text-input v-model="form.date_to" 
							:defaultValue="form.date_to"
							:required="true"
							type="date"
							label="Date to:"
							name="date_to"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('date_to')">
						</text-input>
					</div>
					<div class="col cust-input small-select">
						<selector-input :potentialData="types"
							v-model="selectedType" 
							:defaultData="selectedType"
							placeholder="Select type"
							:required="true"
							label="Type"
							name="type"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('type')">
						</selector-input>
					</div>
					<button type="button" class="btn btn-primary" @click="submit">Refresh</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: ['customer_type', 'from', 'to'],

		data() {
			return {
				form: new Form({
					date_to: this.to? this.to: moment().format("YYYY-MM-DD"),
					date_from: this.from? this.from: moment().startOf('month').format("YYYY-MM-DD"),
					type: this.customer_type? this.customer_type:'Outstanding',
				}),	
				selectedType: {label: this.customer_type? this.customer_type:'Outstanding', value: this.customer_type? this.customer_type:'Outstanding'},
				types: [
						{label: 'Outstanding', value: 'Outstanding'},
						{label: 'Zero balance', value: 'Zero balance'},
						],
				
			};
		},

		mounted() {
		},

		methods: {
			submit() {
				axios.get(this.url)
				.then(response => this.onSuccess(response));
				
				window.location.href = this.url;
			},

			onSuccess(response) {
				console.log("Success");
				window.events.$emit("reload-table");
			},

			onError(error) {				
			},	
		}, 
		
		computed: {
			url() {
				return "/customers/type-statement?type=" + this.selectedType.value + "&from=" + this.form.date_from + "&to=" + this.form.date_to;
			}, 
		}
	}
</script>