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
              <p><b v-if="!dropoff">Current time</b><b v-else>Created time</b>: {{ currentTime }}</p>
              <text-input v-model="barcode"
                          :defaultValue="barcode"
                          :required="false"
                          type="text"
                          label="Scan barcode"
                          :editable="true"
                          :focus="true"
                          :hideLabel="false"
                          @enter="addBarcode">
              </text-input>
              <selector-input :potentialData="customers"
                              v-model="selectedCustomer"
                              :defaultData="selectedCustomer"
                              placeholder="Select customer"
                              :required="true"
                              label="Customer"
                              name="customer_id"
                              :focus="false"
                              :hideLabel="false"
                              :error="form.errors.get('customer_id')"
                              :isHorizontal="true"
                              addonTooltip="Create new customer"
                              addon="createCustomer"
                              @createCustomer="createCustomer">
              </selector-input>
              <selector-input :potentialData="couriers"
                              v-model="selectedCourier"
                              :defaultData="selectedCourier"
                              placeholder="Select vendor"
                              :required="true"
                              label="Vendor"
                              name="vendor_id"
                              :focus="false"
                              :hideLabel="false"
                              :error="form.errors.get('vendor_id')"
                              :isHorizontal="true">
              </selector-input>
              <textarea-input v-model="form.remarks"
                              :defaultValue="form.remarks"
                              :required="false"
                              type="text"
                              label="Remarks"
                              name="remarks"
                              :editable="true"
                              :focus="false"
                              :hideLabel="false"
                              :isHorizontal="true"
                              :error="form.errors.get('remarks')">
              </textarea-input>
            </div>
            <div class="col">
              <h4>Scanned consignment notes</h4>
              <ul>
                <li v-for="(barcode, index) in barcodes" class="mt-1">
                  {{ barcode }} <button type="button" class="btn btn-small btn-danger ml-2" @click="removeBarcode(index)"><i class="fa fa-times"></i></button>
                </li>
              </ul>
            </div>
          </div>
          <div class="row">
            <div class="col">

              <button class="btn btn-primary" type="submit" :disabled="!canSubmit">Submit (F7)</button>
            </div>
          </div>
        </form>
      </div>
      <customers-dialog :data="auth_user" @customerCreated="addCustomer"></customers-dialog>
      <confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
    </div>
  </div>
</template>

<script>

import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
import moment from "moment";

export default {
  props: ['auth_user', 'dropoff'],

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
        barcodes: "",
        customer_id: "",
        vendor_id: "",
        remarks: "",
      })
    };
  },

  mounted() {
    this.getCustomers();

    if(!this.dropoff) {
      this.currentTime = moment().format('LL LTS');
      setInterval(() => this.updateCurrentTime(), 1000);
    }

    window.addEventListener('keydown', function(event){
      if(event.key == "F7") {
        if(this.canSubmit)
          this.submit();
      }
    }.bind(this));
  },

  methods: {
    updateCurrentTime() {
      this.currentTime = moment().format('LL LTS');
    },

    getCustomers(error = 'No error') {
      // console.log(error);
      axios.get("/customers/list")
          .then(response => this.setCustomers(response))
          .catch(error => this.getCustomers(error));
    },

    setCustomers(response) {
      this.customers = response.data.map(function(customer){
        let obj = {};

        obj['value'] = customer.id;
        obj['label'] = customer.name;
        obj['type'] = customer.type;
        obj['customer_group_id'] = customer.customer_group_id;

        return obj;
      });

      this.getCourier();
    },

    getCourier() {
      axios.get("/data/vendors")
          .then(response => this.setCourier(response))
          .catch(error => this.getCourier());
    },

    setCourier(response) {
      this.couriers = response.data.map(function(type){
        let obj = {};

        obj['label'] = type.name;
        obj['value'] = type.id;
        obj['formula'] = type.formula;

        return obj;
      });

    },

    addBarcode() {
      let code = this.barcode.trim();
      if(code && !this.barcodes.includes(code)) {
        this.barcodes.push(code);
      }
      this.barcode = "";
    },

    removeBarcode(index) {
      this.barcodes.splice(index, 1);
    },

    confirmSubmit() {
      this.form.barcodes = this.barcodes;
      this.form.customer_id = this.selectedCustomer.value;
      this.form.vendor_id = this.selectedCourier.value;

      this.form.post("/dropoffs/create")
          .then(response => this.onSuccess(response))
          .catch(error => this.onError(error));
    },

    onSuccess(response) {
      if(response.redirect_url) {
        window.open(response.redirect_url, "_blank");

        window.open(response.admin_redirect_url, "_blank");

        setInterval(function () {
          window.location.href = "/dropoffs/create";
        }, 3000);
      }
    },

    onError(error) {

    },

    submit() {
      this.secondary_message = "Confirm creating dropoff record for " + this.barcodes.length + " package?";
      this.isConfirming = true;
    },

    createCustomer() {
      window.events.$emit('createCustomer');
    },

    addCustomer(e) {
      let customer = {};

      customer['value'] = e.customer.id;
      customer['label'] = e.customer.name;
      customer['type'] = e.customer.type;

      this.customers.push(customer);

      this.selectedCustomer = customer;
    },
  },

  computed: {
    title() {
      return "New drop off";
    },

    canSubmit() {
      return this.barcodes.length > 0 && this.selectedCustomer;
    }
  },
}
</script>