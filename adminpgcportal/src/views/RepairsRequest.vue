<template>
  <h1 class="card-header pb-3">Repairs Request</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Reference No.</th>
              <th scope="col">Date Requested</th>
              <th scope="col">Requestor</th>
              <th scope="col">Department</th>
              <th scope="col">Division</th>
              <th scope="col">Type of Device</th>
              <th scope="col">Initial Problems Encountered</th>
              <th scope="col">Contact No.</th>
              <th scope="col">Name of User</th>
              <th scope="col">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="!items || items.length === 0">
              <td colspan="10" class="text-center">No Upcoming ICT Repair Requests</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td>{{ item.ReferenceNo }}</td>
              <td>
                <div v-html="formatDate(item.DateRequested)"></div>
              </td>
              <td>{{ item.Employee }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.Device }}</td>
              <td>{{ item.InitialProblemsEncountered }}</td>
              <td>{{ item.contactno }}</td>
              <td>{{ item.Name_of_User }}</td>

              <td>
                <v-tooltip text="Approve" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      class="mr-1"
                      icon="mdi-wrench-check-outline"
                      size="small"
                      color="#14727a"
                      @click="OpenModal(item)"
                    >
                    </v-btn>
                  </template>
                </v-tooltip>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <v-dialog v-model="Modal" persistent width="526">
    <v-card>
      <v-card-title class="custom-title"> Confirmation </v-card-title>

      <v-card-text>
        {{ `Are you sure you want to approve this request?` }}
      </v-card-text>

      <!-- Divider -->
      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="Modal = false"
        >
          Cancel
        </v-btn>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="UpdateStatus()"
          :loading="btnLoading"
        >
          Yes
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */

import axios from "axios";
import "bootstrap";
import { format } from "date-fns";
import MySnackBar from "@/components/MySnackBar.vue";
import { ref, onMounted, onBeforeUnmount } from "vue";
import echo from "./echo";

export default {
  name: "FooTer",
  components: {
    MySnackBar,
  },
  data: () => ({
    fetchLoading: false,
    btnLoading: false,
    items: [],
    Modal: false,

    payload: {
      id: "",
    },
    messages: [],
    channel: null,
  }),

  created() {},

  mounted() {
    this.fetch();
    this.channel = echo.channel("portal-notifications").listen("PortalNotification", (e) => {
      if (e.message === "triggerPendingRepairs") {
        console.log(e.message);
        this.fetch();
      }
    });
  },

  beforeUnmount() {
    if (this.channel) {
      this.channel.stopListening("PortalNotification");
    }
  },

  methods: {
    fetchNotif() {
      return;
    },
    fetchNotif1() {
      return;
    },

    OpenModal(item) {
      this.Modal = true;
      this.payload.id = item.ID;
    },

    formatDate(date) {
      const d = new Date(date);
      const datePart = format(d, "MMM dd, yyyy");
      const timePart = format(d, "h:mm a");
      return `${datePart}<br>${timePart}`; // using HTML line break
    },

    UpdateStatus() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCRepairs/ApproveReq",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.id,
          status: 6, // Assuming 6 is the status for approved
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.fetchNotif1();
          this.$refs.MySnackBar.showSuccessMessage("The request has been approved.");
        })
        .catch((err) => {
          console.error(err);
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
      this.Modal = false;
      this.btnLoading = false;
    },

    searchItems() {
      this.fetch("search");
    },

    handleSearchInput() {
      clearTimeout(this.searchTimeout);

      this.searchTimeout = setTimeout(() => {
        this.searchItems();
      }, 500);
      this.myPagination.page = 1;
    },

    fetch() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCRepairs/fetchRequest",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.items = resp.data;

          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
    },
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}

thead th {
  vertical-align: middle;
  background-color: #303847;
  color: white;
}

.custom-title {
  background-color: #14727a;
  color: white;
}

.v-divider {
  background-color: #e0e0e0;
  /* Replace with your desired divider color */
  height: 2px;
  /* Adjust the height of the divider */
  margin: 5px 0;
  /* Adjust the margin above and below the divider */
}
</style>
