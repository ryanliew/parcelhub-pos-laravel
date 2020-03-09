<template>
	<div>
		<div class="card" id="import-header">
			<div class="card-body">            
                <!-- <div class="row">
                    <div class="col">
                        <text-input v-model="form.recipient_name" 
							:defaultValue="form.recipient_name"
							:required="false"
							type="text"
							label="Recipient name"
							name="recipient_name"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="true"
							:error="form.errors.get('recipient_name')">
						</text-input>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col">
                        <text-input v-model="form.phone" 
							:defaultValue="form.phone"
							:required="true"
							type="text"
							label="Phone number"
							name="phone"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="false"
							:error="form.errors.get('phone')">
						</text-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <text-input v-model="form.tracking_code" 
							:defaultValue="form.tracking_code"
							:required="true"
							type="text"
							label="Tracking code"
							name="tracking_code"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:isHorizontal="false"
							:error="form.errors.get('tracking_code')">
						</text-input>
                    </div>
                </div>
                <div class="row" style="float:right">
                    <div class="col">
				        <button @click="checkInParcels()">Check in</button>
                    </div>
                </div>
            </div>		
		</div>
	</div>
</template>

<script>
	export default {
		props: ["created_by"],
		data() {
			return {
				form: new Form({
                    //recipient_name: this.created_by,
                    phone: "",
                    tracking_code: "",
				}),	
			};
		},

		mounted() {
		},

		methods: {
            checkInParcels() {				
				let url = "parcels/checkin";
				this.form.post(url)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
                console.log("Success");
                window.events.$emit("reload-table");
			},

			onError(error) {				
			},		
		}		
	}
</script>