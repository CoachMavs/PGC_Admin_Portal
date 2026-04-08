<template>
  <h1 class="card-header pb-3">Repairs for Receiving</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <v-row rows="auto">
          <v-col cols="12" md="10">
            <v-text-field
              v-model="searchkey"
              label="Search"
              clearable
              @update:model-value="handleSearchInput"
            >
              <template v-slot:append-inner>
                <v-icon @click="handleSearchClick" class="cursor-pointer"
                  >mdi-magnify</v-icon
                >
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" md="2">
            <v-text-field
              v-model="totalRecords"
              label="Total record(s) found:"
              append-inner-icon="mdi-counter"
              readonly
            />
          </v-col>
        </v-row>
      </div>

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
              <td colspan="10" class="text-center">No for Receiving ICT Repairs</td>
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
                <v-tooltip text="Receive" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      class="mr-1 mb-1"
                      icon="mdi-account-wrench"
                      size="small"
                      color="#14727a"
                      flat
                      @click="OpenDialog(item)"
                    >
                    </v-btn>
                  </template>
                </v-tooltip>

                <v-tooltip text="Delete" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      class="mr-1 mb-1"
                      icon="mdi-trash-can-outline"
                      size="small"
                      color="#14727a"
                      flat
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
  <v-dialog v-model="dialog" persistent width="700">
    <v-card>
      <v-card-title class="custom-title"> Repair Details </v-card-title>
      <v-card-text>
        <v-form v-model="form" ref="form">
          <v-container>
            <!-- <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="payload.id"
                  label="ID"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  readonly
                />
              </v-col>
            </v-row> -->
            <v-row>
              <v-col cols="12" md="5">
                <v-menu
                  v-model="dateMenu"
                  :close-on-content-click="false"
                  min-width="auto"
                  :attach="null"
                >
                  <template #activator="{ props }">
                    <v-text-field
                      v-model="payload.dateRcvdView"
                      label="Date Received"
                      readonly
                      v-bind="props"
                      color="#14727a"
                      variant="outlined"
                      hide-details="auto"
                      required
                      :rules="[required]"
                    />
                  </template>
                  <v-card>
                    <v-row justify="space-around" class="ma-2">
                      <v-date-picker v-model="date" color="#14727a" />
                      <v-time-picker v-model="time" format="24" color="#14727a" />
                    </v-row>
                    <v-card-actions>
                      <v-spacer />
                      <v-btn color="#14727a" @click="setDateTimeAndClose">OK</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </v-col>
              <v-col cols="12" md="7">
                <v-select
                  v-model="payload.assignedto"
                  :items="cmb"
                  item-value="emp_no"
                  item-title="empISU"
                  label="Assigned To"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  :rules="[required]"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="8">
                <v-text-field
                  v-model="payload.Name_of_User"
                  label="Name of User"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  readonly
                />
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="payload.ContactNo"
                  label="Contact No."
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  :rules="[required]"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="payload.Department"
                  label="Department"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  readonly
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="payload.Division"
                  label="Division"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  readonly
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-combobox
                  v-model="payload.Device"
                  :items="devices"
                  label="Device"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  :rules="[required]"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="payload.BrandModel"
                  :placeholder="'ex: EPSON L3110'"
                  label="Brand/Model"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  :rules="[required]"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="payload.InitialProblemsEncountered"
                  label="Initial Problems Encountered"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  rows="1"
                  required
                  :rules="[required]"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="payload.OtherInfo"
                  label="Other Info"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  rows="1"
                />
              </v-col>
            </v-row>
          </v-container>
          <v-divider></v-divider>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="
            clearInputs();
            dialog = false;
          "
        >
          Close
        </v-btn>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="update()"
          :loading="btnLoading"
          type="submit"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Modal -->
  <v-dialog v-model="Modal" persistent width="526">
    <v-card>
      <v-card-title class="custom-title"> Confirmation </v-card-title>

      <v-card-text>
        {{ `Are you sure you want to delete this record?` }}
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
          @click="DeleteReq()"
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
import { VTimePicker } from "vuetify/labs/VTimePicker";
import echo from "./echo";
import { ref, onMounted, onBeforeUnmount } from "vue";

const now = new Date();

export default {
  name: "FooTer",
  components: {
    MySnackBar,
    VTimePicker,
  },

  data: () => ({
    dateMenu: false,

    date: now.toISOString().split("T")[0], // Format: YYYY-MM-DD
    time: now.toTimeString().split(" ")[0], // Format: HH:MM:SS
    dateTime: null,

    fetchLoading: false,
    btnLoading: false,
    items: [],

    cmb: [],

    devices: [
      "CCTV",
      "Keyboard",
      "Laptop",
      "Monitor",
      "Mouse",
      "Others",
      "Printer",
      "Scanner",
      "System Unit (CPU)",
      "UPS",
    ],

    Modal: false,
    dialog: false,

    payload: {
      id: "",

      Name_of_User: "",
      Department: "",
      Division: "",
      ContactNo: "",
      Device: "",
      BrandModel: "",
      InitialProblemsEncountered: "",
      assignedto: "",
      OtherInfo: "",
      dateRcvd: format(now, "yyyy-MM-dd HH:mm:ss"),
      dateRcvdView: format(now, "MMMM dd, yyyy h:mm a"),
      emp_no: "",
    },

    searchkey: "",
    totalRecords: 0,
  }),

  setup() {
    function required(v) {
      return !!v || "Field is required";
    }
    return { required };
  },

  created() {},

  mounted() {
    this.fetch();
    this.fetchTech();

    this.channel = echo.channel("chat").listen(".message.sent", (e) => {
      if (e.message === "triggerForReceivingRepairs") {
        this.fetch();
      }
    });
  },

  beforeUnmount() {
    if (this.channel) {
      this.channel.stopListening(".message.sent");
    }
  },
  methods: {
    fetchNotif() {
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCNotifications/triggerForReceivingRepairs",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {})
        .catch((err) => {
          console.error(err.response);
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
    },

    fetchNotif1() {
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCNotifications/triggerCurrentRepairs",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {})
        .catch((err) => {
          console.error(err.response);
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
    },

    update() {
      this.btnLoading = true;
      if (!this.$refs.form.validate()) {
        return;
      }
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCRepairs/ReceiveReq",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.id,
          dateRcvd: this.payload.dateRcvd,
          assignedto: this.payload.assignedto,
          ContactNo: this.payload.ContactNo,
          Device: this.payload.Device,
          BrandModel: this.payload.BrandModel,
          InitialProblemsEncountered: this.payload.InitialProblemsEncountered,
          ProblemsEncountred: this.payload.InitialProblemsEncountered,
          OtherInfo: this.payload.OtherInfo,
          emp_no: this.payload.emp_no,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.fetchNotif1();
          this.$refs.MySnackBar.showSuccessMessage(
            "Device has been received, and the technician in charge has been successfully assigned!"
          );
          this.clearInputs();
        })
        .catch((err) => {
          console.error(err);
          if (err.response.status === 422) {
            this.$refs.MySnackBar.showErrorMessage("Please fill up required fields");
          } else {
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
          }
        });
      this.btnLoading = false;
    },

    clearInputs() {
      this.payload.id = "";
      this.payload.dateRcvd = "";
      this.payload.dateRcvdView = "";
      this.payload.Name_of_User = "";
      this.payload.Department = "";
      this.payload.Division = "";
      this.payload.ContactNo = "";
      this.payload.Device = "";
      this.payload.BrandModel = "";
      this.payload.InitialProblemsEncountered = "";
      this.payload.assignedto = "";
      this.payload.OtherInfo = "";
      this.payload.emp_no = "";
      this.dialog = false;
    },

    OpenDialog(item) {
      this.dialog = true;
      this.payload.id = item.ID;
      this.payload.Name_of_User = item.Name_of_User;
      this.payload.Department = item.DeptDesc;
      this.payload.Division = item.DivDesc;
      this.payload.ContactNo = item.contactno;
      this.payload.Device = item.Device;
      this.payload.BrandModel = item.Brand_and_Model;
      this.payload.InitialProblemsEncountered = item.InitialProblemsEncountered;
      this.payload.emp_no = item.emp_no;

      const now = new Date();
      this.payload.dateRcvd = format(now, "yyyy-MM-dd HH:mm:ss");
      this.payload.dateRcvdView = format(now, "MMMM dd, yyyy h:mm a");
    },

    OpenModal(item) {
      this.Modal = true;
      this.payload.id = item.ID;
    },

    DeleteReq() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCRepairs/DeleteReq",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.id,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.$refs.MySnackBar.showSuccessMessage("Record succesfully deleted!");
        })
        .catch((err) => {
          console.error(err);
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
      this.Modal = false;
      this.btnLoading = false;
    },

    formatDate(date) {
      const d = new Date(date);
      const datePart = format(d, "MMM dd, yyyy");
      const timePart = format(d, "h:mm a");
      return `${datePart}<br>${timePart}`; // using HTML line break
    },

    setDateTimeAndClose() {
      if (this.date && this.time) {
        this.payload.dateRcvd = format(this.date, "yyyy-MM-dd") + " " + this.time;
        this.payload.dateRcvdView = format(
          new Date(this.payload.dateRcvd),
          "MMMM dd, yyyy h:mm a"
        );

        this.dateMenu = false;
      }
    },

    searchItems() {
      this.fetch("search");
    },

    handleSearchInput() {
      clearTimeout(this.searchTimeout);

      this.searchTimeout = setTimeout(() => {
        this.searchItems();
      }, 500);
    },

    // fetch1() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "PGCRepairs/fetchForReceiving",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       this.items = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
    //     });
    // },

    fetch(paramType = null) {
      let myParameter = {
        searchkey: "",
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCRepairs/fetchForReceiving",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("xxx"),
          },
          params: myParameter,
        })
          .then((resp) => {
            this.items = resp.data;

            this.totalRecords = resp.data.length;
            this.fetchLoading = false;
          })
          .catch((err) => {
            this.fetchLoading = false;
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!", err);
          });
      };

      if (paramType == null) {
        myParameter = {
          searchkey: this.searchkey,
        };
        loadData();
      } else if (paramType == "search") {
        myParameter = {
          searchkey: this.searchkey,
        };
        loadData();
      }
    },

    fetchTech() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCRepairs/fetchTech",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.cmb = resp.data;

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

.v-divider {
  background-color: #e0e0e0;
  /* Replace with your desired divider color */
  height: 2px;
  /* Adjust the height of the divider */
  margin: 5px 0;
  /* Adjust the margin above and below the divider */
}

.custom-title {
  background-color: #14727a;
  color: white;
}
</style>
