<template>
  <div>
    <div class="card" id="import-header">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <input class="file-input" type="file" ref="file" name="file" @change="fileUploaded">
          </div>
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
        <button class="btn btn-primary" @click="importFromExcel()" :disabled="!form.file">Import</button>
        <i v-if="processing" class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
        <a href="/billing.xlsx" target="_blank">
          <button class="btn btn-primary" style="float: right;" title="Download sample excel">Download Template</button>
        </a>
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
      console.log("Success");
      this.processing = false;
      window.events.$emit("reload-table");
    },

    onError(error) {
      this.processing = false;
    },
  }
}
</script>