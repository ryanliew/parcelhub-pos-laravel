<template>
	<div class="card">
		<div class="card-header">

			<h3><b>Cashup preview</b></h3>
			<div class="row align-items-end">
				<div class="col">
					<b>Drawer :</b> {{ cashup.terminal.name }} <br>
					<b>Transactions :</b> {{ cashup.invoice_from }} - {{ cashup.invoice_to }}<br>
					<b>Session opened :</b> {{ formatToDayDateTime(cashup.session_start) }} <br>
					<b>Report run :</b> {{ formatToDayDateTime(cashup.created_at) }} <br>
					<b>Created by :</b> {{ cashup.creator.name }} <br>
					<b>Status: </b><span class="text-capitalize">{{ localStatus }}</span>
				</div>
				<div class="col-narrow">
					<button type="submit" class="btn btn-primary" v-if="localStatus == 'draft'" @click="submitCashup" :title="submitTooltip" :disabled="!canSubmit">Confirm</button>
					<button type="button" class="btn btn-danger" v-if="localStatus == 'draft'" @click="deleteCashup">Delete</button>
					<button type="button" class="btn btn-primary" v-if="localStatus == 'confirmed'" @click="printCashup">Print</button>
				</div>
			</div>
		</div>
		
		<div class="card-body">
			<h3><b>Summary</b></h3>
			<table class="table" id="items">
			  	<tbody>
				  	<tr>
				   		<th>Legend</th>
					    <th>Payment Type</th>
				      	<th>Expected RM</th>
				      	<th>Actual RM</th>
				      	<th>%</th>
				      	<th>Count</th>
				  	</tr>

				  	<tr class="item-row" v-for="(detail, index) in cashup.details">
				  		<td>{{ detail.legend }}</td>
				  		<td>{{ detail.type }}</td>
				  		<td>{{ detail.expected_amount | price }}</td>
				  		<td v-if="localStatus == 'draft' && form.actuals[index]">
				  			<text-input v-model="form.actuals[index].actual_amount" 
				  				:defaultValue="form.actuals[index].actual_amount"
				  				:required="true"
				  				type="number"
				  				label="Actual amount"
				  				name="actual_amount"
				  				:editable="true"
				  				:focus="false"
				  				:hideLabel="true"
				  				:error="form.errors.get('actual_amount')">
				  			</text-input>
				  		</td>
				  		<td v-else>
				  			{{ detail.actual_amount | price }}
				  		</td>
				  		<td>{{ detail.percentage.toFixed(2) }}</td>
				  		<td>{{ detail.count }}</td>
				  	</tr>

				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td><b>Total</b></td>
						<td><b>{{ cashup.total | price }}</b></td>
						<td><b>{{ actual | price }}</b></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table> 

			<h3><b>Including invoices</b></h3>
			<table class="table" id="invoices">
			  	<tbody>
				  	<tr>
				   		<th>Invoice no</th>
              <th>Date</th>
					    <th>Payment Type</th>
				      	<th>Expected RM</th>
				      	<th>%</th>
				      	<th>Payment #</th>
				  	</tr>
				  	<tr class="item-row" v-for="invoice in cashup.invoices">		
				  		<td>{{ invoice.invoice_no }}</td>
              <td>{{ invoice.created_at | date }}</td>
				  		<td>{{ invoice.pivot.payment_method }}</td>
				  		<td>{{ invoice.pivot.total | price }}</td>
				  		<td>{{ cashup.total > 0 ? ( invoice.pivot.total / cashup.total * 100 ) : 0.00 | price }}</td>
				  		<td>{{ invoice.pivot.payment_id ? invoice.pivot.payment_id : 'N/A' }}</td>
				  	</tr>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
            <td></td>
						<td><b>Total</b></td>
						<td><b>{{ cashup.total > 0 ? cashup.total - cashup.float_value : 0.00 | price }}</b></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</template>

<script>
	import moment from "moment";
	export default {
		props: ['cashup'],
		data() {
			return {
				form: new Form({
					actuals: [],
					actual_amount: this.cashup.actual_amount
				}),

				localStatus: this.cashup.status
			};
		},

		mounted() {
			this.setForm();
		},

		methods: {
			formatToDayDateTime(date) {
				return moment(date).format("ddd, MMM DD, YYYY, HH:MM");
			},

			setForm() {
				this.cashup.details.forEach(function(detail, index){
					Vue.set(this.form.actuals, index, {type: detail.type, actual_amount: detail.actual_amount});
				}.bind(this));
			},

			submitCashup() {
				this.form.actual_amount = this.actual;
				this.form.post('/cashups/confirm/' + this.cashup.id)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
				this.printCashup();
				this.localStatus = 'confirmed';	
				window.location.reload();
			},

			onError(error) {

			},

			deleteCashup() {
				axios.post("/cashups/delete/" + this.cashup.id)
					.then(response => this.onSuccessDelete(response))
					.catch(error => this.onError(error));
			},

			onSuccessDelete(response) {
				flash(response.data.message);
				setInterval(function(){
					window.location.href = "/cashups";
				}, 3000);
				
			},

			printCashup() {
				window.open('/cashups/report/' + this.cashup.id);
			}
		},

		computed: {
			actual() {
				return _.sumBy(this.form.actuals, function(value){ return parseFloat(value.actual_amount); });
			},

			canSubmit() {
				return this.submitTooltip == "";
			},

			submitTooltip() {
				return Number.isNaN(this.actual) ? "Please fill in all actual amount" : "";
			}
		}


	}
</script>