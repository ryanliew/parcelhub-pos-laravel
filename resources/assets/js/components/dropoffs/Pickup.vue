<template>
  <div class="container">
    <div class="card" id="dropoff-header" tabindex="-1" role="dialog">
      <div class="card-body">
        <h2>{{ title }}</h2>
        <form @submit.prevent="submit"
              @keydown="form.errors.clear($event.target.name)"
              @input="form.errors.clear($event.target.name)">
          <div class="row">
            <div class="col">
              <p><b>Current time</b>: {{ currentTime }}</p>

              <h4>Consignment notes</h4>
              <ul>
                <li v-for="(barcode, index) in dropoff.items" class="mt-1">
                  {{ barcode.consignment_note }}
                </li>
              </ul>
              <p><b>Service:</b> {{ dropoff.vendor.name }}</p>
              <p><b>Remark:</b> {{ dropoff.remarks }}</p>
              <p><b>Status:</b> {{ dropoff.status }}</p>
              <p class="text-danger text-center" v-if="dropoff.picked_up_on && !authuser">This dropoff has already been picked up. Please search for assistant from our staff.</p>
              <text-input v-model="form.picked_up_by"
                          :defaultValue="form.picked_up_by"
                          :required="false"
                          type="text"
                          label="Driver name:"
                          :editable="!dropoff.picked_up_by || authuser"
                          :focus="true"
                          :hideLabel="false">
              </text-input>
              <text-input v-model="form.vehicle_no"
                          :defaultValue="form.vehicle_no"
                          :required="false"
                          type="text"
                          label="Vehicle no.:"
                          :editable="!dropoff.vehicle_no || authuser"
                          :focus="false"
                          :hideLabel="false">
              </text-input>
              <text-input v-model="form.picked_up_on"
                          :defaultValue="form.picked_up_on"
                          :required="false"
                          type="datetime-local"
                          label="Pick up time (eg: 2021-01-01 01:00:00):"
                          :editable="authuser"
                          :focus="false"
                          :hideLabel="false"
                          v-if="authuser || dropoff.picked_up_on">
              </text-input>
            </div>
          </div>
          <div class="row">
            <div class="col">

              <button class="btn btn-primary" type="submit" :disabled="!canSubmit">Confirm pickup</button>
              <template v-if="authuser">
                <a :href="'/dropoffs/receipt/' + dropoff.id" target="_blank" class="btn btn-primary" v-if="dropoff">Print customer slip</a>
                <a :href="'/dropoffs/admin/' + dropoff.id" target="_blank" class="btn btn-primary" v-if="dropoff">Print driver slip</a>


                <a href="/dropoffs" v-if="authuser" class="btn btn-secondary">Back</a>
              </template>
            </div>
          </div>
        </form>
      </div>
      <confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
    </div>
  </div>
</template>

<script>

import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
import moment from "moment";

export default {
  props: ['authuser', 'dropoff'],

  mixins: [ConfirmationMixin],

  data() {
    return {
      barcode: "",
      barcodes: [],
      customers: [],
      couriers: [],
      selectedCourier: "",
      selectedCustomer: "",
      currentTime: '',
      form: new Form({
        picked_up_by: "",
        vehicle_no: "",
        picked_up_on: "",
      })
    };
  },

  mounted() {
      this.currentTime = moment().format('LL LTS');
      setInterval(() => this.updateCurrentTime(), 1000);
      this.setForm();
  },

  methods: {
    setForm() {
      if(this.dropoff) {
        this.form.picked_up_on = this.dropoff.picked_up_on;
        this.form.picked_up_by = this.dropoff.picked_up_by;
        this.form.vehicle_no = this.dropoff.vehicle_no;
      }
    },

    updateCurrentTime() {
      this.currentTime = moment().format('LL LTS');
    },

    confirmSubmit() {
      this.form.barcodes = this.barcodes;
      this.form.customer_id = this.selectedCustomer.value;
      this.form.vendor_id = this.selectedCourier.value;

      let url = this.authuser ? "/dropoffs/update/" : "/pickup/";
      url += this.dropoff.id;

      this.form.post(url)
          .then(response => this.onSuccess(response))
          .catch(error => this.onError(error));
    },

    onSuccess(response) {
      this.$swal({
        title: "Pickup success",
        type: 'success',
        showCancelButton: false
      }).then(response => {
        window.location.reload();
      });
    },

    onError(error) {

    },

    submit() {
      this.secondary_message = "Confirm pickup for dropoff " + this.dropoff.dropoff_no + "?";
      this.isConfirming = true;
    },
  },

  computed: {
    title() {
      return "Dropoff - " + this.dropoff.dropoff_no;
    },

    canSubmit() {
      return true;
    }
  },
}
</script>