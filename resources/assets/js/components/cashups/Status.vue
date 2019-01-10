<template>
	<div>
		<form @submit.prevent="submit" 
			@keydown="form.errors.clear($event.target.name)" 
			@input="form.errors.clear($event.target.name)">
			<div class="row align-items-center status-input">
				<div class="col">
					<text-input v-model="form.actual_amount" 
						:defaultValue="form.actual_amount"
						:required="true"
						type="number"
						label="Actual amount"
						name="actual_amount"
						:editable="localStatus == 'draft'"
						:focus="false"
						:hideLabel="false"
						step="0.01"
						:error="form.errors.get('actual_amount')"
						:isHorizontal="true">
					</text-input>
				</div>
				<div class="col-narrow">
					<button type="submit" class="btn btn-primary" v-if="localStatus == 'draft'">Confirm</button>
					<button type="button" class="btn btn-danger" v-if="localStatus == 'draft'" @click="deleteCashup">Delete</button>
					<button type="button" class="btn btn-primary" v-if="localStatus == 'confirmed'" @click="printCashup">Print</button>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
	export default {
		props: ['cashup'],
		data() {
			return {
				form: new Form({
					actual_amount: this.cashup.total
				}),

				localStatus: this.cashup.status
			};
		},

		methods: {
			submit() {
				this.form.post('/cashups/confirm/' + this.cashup.id)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
				window.open('/cashups/report/' + this.cashup.id);
				this.localStatus = 'confirmed';	
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
		}	
	}
</script>	