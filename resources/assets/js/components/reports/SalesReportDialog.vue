<template>
  <div class="modal fade" id="cancel-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Sales report</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submit">
            <text-input
              v-model="from"
              :defaultValue="from"
              :required="true"
              type="date"
              label="From date"
              name="password"
              :editable="true"
              :focus="true"
              :hideLabel="false"
            ></text-input>

            <text-input
              v-model="to"
              :defaultValue="to"
              :required="true"
              type="date"
              label="To date"
              name="password"
              :editable="true"
              :focus="true"
              :hideLabel="false"
            ></text-input>

            <selector-input
              :potentialData="branches"
              v-model="selectedBranch"
              :defaultData="selectedBranch"
              placeholder="Select branch"
              :required="true"
              label="Branch"
              name="branch"
              :editable="true"
              :focus="false"
              :hideLabel="false"
              v-if="user.is_admin"
            ></selector-input>
          </form>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-primary"
            @disabled="!canSubmit"
            @click="submit"
            v-html="action"
          ></button>
          <button
            type="button"
            class="btn btn-primary"
            @disabled="!canSubmit"
            @click="download"
            v-html="excel"
          ></button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
export default {
  props: ["user"],

  data() {
    return {
      from: moment()
        .startOf("month")
        .format("YYYY-MM-DD"),
      to: moment().format("YYYY-MM-DD"),
      branch: this.user.current_branch,
      branches: [],
      selectedBranch: ""
    };
  },

  mounted() {
    window.events.$on("generateSalesReport", evt => this.openDialog(evt));
    this.getBranches();
  },

  methods: {
    getBranches(error = "No error", tries = 0) {
      if (tries < 3)
        axios
          .get("/data/branches")
          .then(response => this.setBranches(response))
          .catch(error => this.getBranches(error, ++tries));
    },

    setBranches(response) {
      this.branches = response.data.map(function(branch) {
        let obj = {};

        obj["value"] = branch.id;
        obj["label"] = branch.name;

        return obj;
      });
      
      var allbranch = {value:"0", label:"All branches"};
      this.branches.unshift(allbranch);

      this.selectedBranch = _.filter(
        this.branches,
        function(branch) {
          return branch.value == this.user.current_branch;
        }.bind(this)
      )[0];
    },

    openDialog(evt) {
      $("#cancel-dialog").modal();
      this.isActive = true;
    },

    closeDialog() {
      this.isActive = false;
    },

    submit() {
      let url = this.url;
      
      if (this.user.is_admin && this.branch) {
        if (this.branch == "0") {
           url += "&allbranch=true";
        }
        else {
          url += "&branch=" + this.branch;
        }
      }
  
      window.location.href = url;
    },

    download() {
      let url = this.url;

      if (this.user.is_admin && this.branch) url += "&branch=" + this.branch;

      url += "&export=true";

      window.location.href = url;

      $("#cancel-dialog").modal("hide");
    }
  },

  computed: {
    url() {
      return "/admin/reports/sales?from=" + this.from + "&to=" + this.to;
    },
    action() {
      return "View";
    },

    excel() {
      return "Download";
    },

    canSubmit() {
      return this.from && this.to;
    }
  },

  watch: {
    selectedBranch(newVal) {
      this.branch = newVal.value;
    }
  }
};
</script>