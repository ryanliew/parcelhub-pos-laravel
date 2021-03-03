<template>
  <div>
    <div class="card" id="import-header">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label>Excel file</label>
              <input class="file-input form-control" type="file" ref="file" name="file" @change="fileUploaded">
            </div>
          </div>
          <div class="col">
            <text-input v-model="form.vendor_name"
                        :defaultValue="form.vendor_name"
                        :required="true"
                        type="text"
                        label="Vendor name"
                        name="vendor_name"
                        :editable="true"
                        :focus="true"
                        :hideLabel="false"
                        :error="form.errors.get('vendor_name')">
            </text-input>
          </div>
          <div class="col">
            <text-input v-model="form.payment_term"
                        :defaultValue="form.payment_term"
                        :required="false"
                        type="number"
                        label="Payment terms (days)"
                        name="payment_term"
                        :editable="true"
                        :focus="true"
                        :hideLabel="false"
                        :error="form.errors.get('payment_term')">
            </text-input>
          </div>
        </div>
        <i v-if="processing" class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
        <div class="row">
          <div class="col">
            <text-input v-model="form.invoice_date"
                        :defaultValue="form.invoice_date"
                        :required="true"
                        type="date"
                        label="Invoice date"
                        name="invoice_date"
                        :editable="true"
                        :focus="true"
                        :hideLabel="false"
                        :error="form.errors.get('invoice_date')">
            </text-input>
          </div>
          <div class="col">
            <text-input v-model="form.billing_start"
                        :defaultValue="form.billing_start"
                        :required="true"
                        type="date"
                        label="Billing start"
                        name="billing_start"
                        :editable="true"
                        :focus="true"
                        :hideLabel="false"
                        :error="form.errors.get('billing_start')">
            </text-input>
          </div>
          <div class="col">
            <text-input v-model="form.billing_end"
                        :defaultValue="form.billing_end"
                        :required="true"
                        type="date"
                        label="Billing end"
                        name="billing_end"
                        :editable="true"
                        :focus="true"
                        :hideLabel="false"
                        :error="form.errors.get('billing_end')">
            </text-input>
          </div>
        </div>
        <div class="row">
          <div class="col-8">

          </div>
          <div class="col">
            <button class="btn btn-primary float-right ml-2" @click="importFromExcel()" :disabled="!form.file">Import</button>
            <a class="btn btn-secondary float-right" title="Download sample excel" href="/billing.xlsx" target="_blank">
              Download Template
            </a>

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
        created_by: this.created_by,
        fileName: "",
        file: "",
        invoice_date: "",
        billing_start: '',
        billing_end: "",
        vendor_name: "",
        payment_term: "",
      }),
      processing: false,
    };
  },

  mounted() {

  },

  methods: {
    fileUploaded() {
      this.form.file = this.$refs.file.files[0];
    },

    importFromExcel() {
      this.processing = true;
      let url = "billings/import";
      this.form.post(url)
          .then(response => this.onSuccess(response))
          .catch(error => this.onError(error));
    },

    onSuccess(response) {
      this.processing = false;
      window.events.$emit("reload-table");
    },

    onError(error) {
      this.processing = false;
    },
  }
}
</script>