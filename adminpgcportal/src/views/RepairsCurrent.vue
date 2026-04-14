<template>
  <h1 class="card-header pb-3">Current Repairs</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <v-row rows="auto">
          <v-col cols="12" md="7">
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
            <v-select
              v-model="assignedFilter"
              :items="['All', 'Only me']"
              label="Assigned to:"
              append-inner-icon="mdi-filter-outline"
              dense
              @update:model-value="handleSearchInput"
            />
          </v-col>

          <v-col cols="12" md="2">
            <v-text-field
              v-model="totalRecords"
              label="Total record(s) found:"
              append-inner-icon="mdi-counter"
              readonly
            />
          </v-col>

          <v-col cols="12" md="1">
            <div class="text-center">
              <v-btn
                color="#14727a"
                @click="OpenDialogAdd()"
                style="height: 55px; width: 100%"
                block
              >
                <div class="d-flex flex-column align-start">
                  <span>Add</span>
                  <span>Repair</span>
                </div>
                <v-icon class="ml-2">mdi-plus</v-icon>
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Top Scrollbar -->
      <div class="table-scroll-top" ref="tableScrollTop">
        <div class="scroll-content"></div>
      </div>

      <!-- Table with Bottom Scrollbar -->
      <div class="table-responsive" style="white-space: auto" ref="tableScrollBottom">
        <div style="height: 12px"></div>

        <table class="table" style="table-layout: fixed; min-width: 2500px">
          <thead>
            <tr>
              <th style="width: 180px; text-align: center">Actions</th>
              <th style="width: 180px; text-align: center">Reference No.</th>
              <th scope="col">Date Received</th>
              <!-- <th scope="col">Days to Repair</th> -->
              <th scope="col">Type of Device</th>
              <th scope="col">Brand and Model</th>
              <th scope="col">Problems Encountered</th>
              <th scope="col">Actions Taken</th>
              <th scope="col">Status</th>
              <th scope="col">Name of User</th>
              <th scope="col">Requestor</th>
              <th scope="col">Department</th>
              <th scope="col">Division</th>
              <th scope="col">Contact No.</th>
              <th scope="col">Other Device Info</th>
              <th scope="col">Received By</th>
              <th scope="col">Assigned To</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td style="width: 100px; max-width: 100px; text-align: center">
                <v-menu offset-y v-if="item.AssignedTo_emp_no === empISU">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      icon="mdi-dots-vertical"
                      size="small"
                      color="#14727a"
                      flat
                      aria-label="Actions"
                    />
                  </template>
                  <v-list>
                    <!-- <v-list-item @click="OpenDialogAdd(item)">
                    <v-list-item-icon>
                      <v-icon>mdi-plus-box</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Add</v-list-item-title>
                  </v-list-item> -->
                    <v-list-item @click="OpenDialog(item)">
                      <v-list-item-icon>
                        <v-icon>mdi-pencil</v-icon>
                      </v-list-item-icon>
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="OpenModal(item)">
                      <v-list-item-icon>
                        <v-icon>mdi-delete</v-icon>
                      </v-list-item-icon>
                      <v-list-item-title>Delete</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="OpenModalActionsTaken(item)">
                      <v-list-item-icon>
                        <v-icon>mdi-check-circle</v-icon>
                      </v-list-item-icon>
                      <v-list-item-title>Actions Taken</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="OpenModalStatus(item)">
                      <v-list-item-icon>
                        <v-icon>mdi-swap-horizontal</v-icon>
                      </v-list-item-icon>
                      <v-list-item-title>Change Status</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </td>

              <td style="text-align: center">{{ item.ReferenceNo }}</td>
              <td>
                <div v-html="formatDateTable(item.DateReceived)"></div>
              </td>
              <!-- <td>0</td> -->
              <td>{{ item.Device }}</td>
              <td>{{ item.Brand_and_Model }}</td>
              <td>{{ item.ProblemsEncountred }}</td>
              <td style="width: 600px; word-break: break-word; white-space: normal">
                {{ item.ActionsTaken }}
              </td>
              <td>{{ item.RepairStatus }}</td>
              <td>{{ item.Name_of_User }}</td>
              <td>{{ item.Employee }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.contactno }}</td>
              <td>{{ item.OtherDevInfo }}</td>
              <td>{{ item.Receivedby }}</td>
              <td>{{ item.AssignedTo }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center">
      <v-pagination
        v-model="myPagination.page"
        :length="myPagination.total"
        :total-visible="$vuetify.display.smAndDown ? 1 : 7"
        :size="$vuetify.display.smAndDown ? 'small' : 'default'"
        @update:model-value="fetch('page')"
        rounded="circle"
        color="#673AB7"
        class="my-pagination"
      ></v-pagination>
    </div>
  </div>

  <!-- Modal -->
  <v-dialog v-model="dialog" persistent width="700">
    <v-card>
      <v-card-title class="custom-title"> Repair Details </v-card-title>

      <v-card-text>
        <v-form ref="form" v-model="form">
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
                  :close-on-content-click="true"
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
                  validate-on="blur"
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
                  required
                  :rules="[required]"
                  validate-on="blur"
                >
                  <template v-slot:append-inner>
                    <v-icon @click="dialogList = true" class="cursor-pointer"
                      >mdi-magnify</v-icon
                    >
                  </template>
                </v-text-field>
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
                  validate-on="blur"
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
                  validate-on="blur"
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
                  validate-on="blur"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="payload.ProblemsEncountred"
                  label="Problems Encountered"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  rows="1"
                  required
                  :rules="[required]"
                  auto-grow
                  validate-on="blur"
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
                  auto-grow
                  validate-on="blur"
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
          @click="clearInputs()"
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

  <!-- End of Modal -->

  <!-- Modal with Data Table -->
  <v-dialog v-model="dialogList" max-width="800px">
    <v-card>
      <v-card-title
        style="background-color: #14727a; color: white"
        class="d-flex align-center justify-space-between"
      >
        <span>Select Record</span>
        <v-btn
          icon
          variant="text"
          style="background: transparent"
          @click="dialogList = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-text-field
          v-model="userSearch"
          label="Search user"
          clearable
          class="mb-3"
          prepend-inner-icon="mdi-magnify"
        />
        <v-data-table
          class="my-table elevation-1"
          header-class="my-table-header"
          v-if="filteredUsers.length"
          :headers="headers"
          :items="filteredUsers"
          item-value="emp_no"
        >
          <template v-slot:item="{ item }">
            <tr
              @click="
                payloadStatus.CheckStatus ? selectReturnedTo(item) : selectUser(item)
              "
              style="cursor: pointer"
            >
              <td>{{ item.emp_no }}</td>
              <td>{{ item.full_name }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
            </tr>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-dialog>

  <!-- End of Modal -->

  <!-- Modal Actions Taken -->

  <v-dialog v-model="dialogActions" max-width="600px">
    <v-card>
      <v-card-title
        style="background-color: #14727a; color: white"
        class="d-flex align-center justify-space-between"
      >
        <span>Actions Taken</span>
        <v-btn
          icon
          variant="text"
          style="background: transparent"
          @click="
            payload.actionsTaken = '';
            dialogActions = false;
          "
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text>
        <table class="table my-table elevation-1 actions-taken-table" style="width: 100%">
          <thead>
            <tr>
              <th>Date</th>
              <th>Actions Taken</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in actionsTaken" :key="item.id">
              <td>
                <div v-html="formatDateTable(item.DDate)"></div>
              </td>
              <td>{{ item.ActionTaken }}</td>
            </tr>
            <tr v-if="!actionsTaken.length">
              <td colspan="2" class="text-center">No actions taken yet.</td>
            </tr>
          </tbody>
        </table>
      </v-card-text>

      <v-divider></v-divider>

      <v-container>
        <v-card-title class="custom-title mb-2"> Add Actions Taken </v-card-title>
        <v-row>
          <v-col cols="12">
            <v-textarea
              v-model="payload.actionsTaken"
              label="Actions Taken"
              color="#14727a"
              variant="outlined"
              hide-details="auto"
              rows="1"
              required
              :rules="[required]"
              class="ma-2"
              auto-grow
            />
          </v-col>
        </v-row>
      </v-container>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="
            payload.actionsTaken = '';
            dialogActions = false;
          "
        >
          Close
        </v-btn>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="AddActions()"
          :loading="btnLoading"
          type="submit"
        >
          Add
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- End of Modal Actions Taken -->

  <!-- Modal  Change Status-->

  <v-dialog v-model="dialogStatus" max-width="600px">
    <v-card>
      <v-card-title
        style="background-color: #14727a; color: white"
        class="d-flex align-center justify-space-between"
      >
        <span>Change Status</span>
        <v-btn
          icon
          variant="text"
          style="background: transparent"
          @click="clearpayloadStatus()"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-form ref="form" v-model="form">
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-select
                v-model="payloadStatus.Status_Remarks"
                :items="status"
                item-value="ID"
                item-title="RepairStatus"
                label="Status"
                color="#14727a"
                variant="outlined"
                hide-details="auto"
                required
                :rules="[required]"
                validate-on="blur"
              />
            </v-col>
          </v-row>

          <v-row
            v-if="payloadStatus.Status_Remarks == 4 || payloadStatus.Status_Remarks == 5"
          >
            <v-col cols="12" md="5">
              <v-menu
                v-model="dateMenu"
                :close-on-content-click="false"
                min-width="auto"
                :attach="null"
              >
                <template #activator="{ props }">
                  <v-text-field
                    v-model="payloadStatus.DateReturnedView"
                    label="Date Returned"
                    readonly
                    v-bind="props"
                    color="#14727a"
                    variant="outlined"
                    hide-details="auto"
                  />
                </template>
                <v-card>
                  <v-row justify="space-around" class="ma-2">
                    <v-date-picker v-model="datestatus" color="#14727a" />
                    <v-time-picker v-model="timestatus" format="24" color="#14727a" />
                  </v-row>
                  <v-card-actions>
                    <v-spacer />
                    <v-btn color="#14727a" @click="setDateTimeAndCloseStatus()">OK</v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </v-col>
            <v-col cols="12" md="7">
              <v-text-field
                v-model="payloadStatus.ReturnedTo"
                label="Returned To"
                color="#14727a"
                variant="outlined"
                hide-details="auto"
                readonly
                required
                :rules="[required]"
                validate-on="blur"
              >
                <template v-slot:append-inner>
                  <v-icon @click="OpenDialogReturnedTo()" class="cursor-pointer"
                    >mdi-magnify</v-icon
                  >
                </template>
              </v-text-field>
            </v-col>
          </v-row>

          <v-row v-if="payloadStatus.Status_Remarks == 5">
            <v-col cols="12">
              <v-textarea
                v-model="payloadStatus.Comments"
                label="Reason"
                color="#14727a"
                variant="outlined"
                hide-details="auto"
                rows="1"
                required
                :rules="[required]"
                validate-on="blur"
                auto-grow
              />
            </v-col>
          </v-row>
        </v-card-text>
      </v-form>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="clearpayloadStatus()"
        >
          Close
        </v-btn>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="UpdateStatusChecker()"
          :loading="btnLoading"
          type="submit"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- End of Modal Change Status -->

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
import { ref, onMounted, onBeforeUnmount } from "vue";
import echo from "./echo";

const now = new Date();

export default {
  components: {
    MySnackBar,
    VTimePicker,
  },
  data: () => ({
    dateMenu: false,
    dialogActions: false,
    Modal: false,
    dialog: false,
    dialogStatus: false,

    date: now.toISOString().split("T")[0],
    time: now.toTimeString().split(" ")[0],

    datestatus: now.toISOString().split("T")[0],
    timestatus: now.toTimeString().split(" ")[0],
    dateTimestatus: null,

    datefrom: null, // Initialize as null
    dateto: null, // Initialize as null
    menufrom: false,
    menuto: false,
    fetchLoading: false,
    btnLoading: false,
    items: [],
    users: [],
    actionsTaken: [],
    status: [],

    searchkey: "",
    assignedFilter: "All",
    totalRecords: 0,

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

    headers: [
      { title: "Employee No.", key: "emp_no" },
      { title: "Name", key: "full_name" },
      { title: "Department", key: "DeptDesc" },
      { title: "Division", key: "DivDesc" },
    ],

    Modal: false,
    dialog: false,
    dialogList: false,
    empISU: "",

    payload: {
      id: "",
      dateRcvd: format(now, "yyyy-MM-dd HH:mm:ss"),
      Name_of_User: "",
      Department: "",
      Division: "",
      ContactNo: "",
      Device: "",
      BrandModel: "",
      InitialProblemsEncountered: "",
      ProblemsEncountred: "",
      assignedto: "",
      OtherInfo: "",
      dateRcvdView: format(now, "MMMM dd, yyyy h:mm a"),
      emp_no: "",
      actionsTaken: "",
    },

    payloadStatus: {
      id: "",
      Status_Remarks: "",
      ReturnedTo: "",
      DateReturned: format(now, "yyyy-MM-dd HH:mm:ss"),
      DateReturnedView: format(now, "MMMM dd, yyyy h:mm a"),
      Comments: "",
      ReturnedToNo: "",
      CheckStatus: false,
    },

    myPagination: {
      page: 1,
      total: 5,
      per_page: 0,
    },

    userSearch: "", // <-- Add this line

    messages: [],
    channel: null,
  }),
  computed: {
    filteredUsers() {
      if (!this.userSearch) return this.users;
      const search = this.userSearch.toLowerCase();
      return this.users.filter(
        (u) =>
          (u.full_name && u.full_name.toLowerCase().includes(search)) ||
          (u.emp_no && u.emp_no.toString().includes(search)) ||
          (u.DeptDesc && u.DeptDesc.toLowerCase().includes(search)) ||
          (u.DivDesc && u.DivDesc.toLowerCase().includes(search))
      );
    },
  },

  watch: {
    dialogList(val) {
      if (!val) {
        this.userSearch = "";
      }
    },
  },

  setup() {
    function required(v) {
      return !!v || "Field is required";
    }
    return { required };
  },

  mounted() {
    this.dateto = new Date();
    this.formattedDateTo = this.formatDate(this.dateto);

    // Set "Date From" as 15 days before today
    let pastDate = new Date();
    pastDate.setDate(pastDate.getDate() - 15); // Subtract 15 days
    this.datefrom = pastDate;
    this.formattedDateFrom = this.formatDate(this.datefrom);

    this.fetch();
    this.fetchUsers();
    this.fetchTech();

    // Synchronize scrollbars
    const topScroll = this.$refs.tableScrollTop;
    const bottomScroll = this.$refs.tableScrollBottom;

    topScroll.addEventListener("scroll", () => {
      bottomScroll.scrollLeft = topScroll.scrollLeft;
    });

    bottomScroll.addEventListener("scroll", () => {
      topScroll.scrollLeft = bottomScroll.scrollLeft;
    });

    this.channel = echo.channel("portal-notifications").listen("PortalNotification", (e) => {
      if (e.message === "triggerCurrentRepairs") {
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

    AddActions() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCRepairs/AddActions",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          repairlogID: this.payload.id,
          actionsTaken: this.payload.actionsTaken,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.fetchActionsTaken();
          this.$refs.MySnackBar.showSuccessMessage("Actions taken successfully updated!");
          this.payload.actionsTaken = "";
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

    update() {
      if (!this.$refs.form.validate()) {
        return;
      }
      this.btnLoading = true;
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
          emp_no: this.payload.emp_no,
          Device: this.payload.Device,
          BrandModel: this.payload.BrandModel,
          InitialProblemsEncountered: this.payload.InitialProblemsEncountered,
          ProblemsEncountred: this.payload.ProblemsEncountred,
          OtherInfo: this.payload.OtherInfo,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
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

    UpdateStatusChecker() {
      if (!this.$refs.form.validate()) {
        return;
      }

      if (this.payloadStatus.Status_Remarks == 4) {
        this.UpdateStatusRepaired();
      } else if (this.payloadStatus.Status_Remarks == 5) {
        this.UpdateStatusNotRepaired();
      } else {
        this.UpdateStatus();
      }
    },

    UpdateStatusRepaired() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCRepairs/UpdateStatusRepaired",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payloadStatus.id,
          status: this.payloadStatus.Status_Remarks,
          DateReturned: this.payloadStatus.DateReturned,
          ReturnedToNo: this.payloadStatus.ReturnedToNo,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.clearpayloadStatus();
          this.$refs.MySnackBar.showSuccessMessage("Status succesfully updated.");
        })
        .catch((err) => {
          console.error(err);
          if (err.response.status === 422) {
            this.$refs.MySnackBar.showErrorMessage("Please fill up required fields");
          } else {
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
          }
        });
      this.Modal = false;
      this.btnLoading = false;
    },

    UpdateStatusNotRepaired() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCRepairs/UpdateStatusNotRepaired",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payloadStatus.id,
          status: this.payloadStatus.Status_Remarks,
          DateReturned: this.payloadStatus.DateReturned,
          ReturnedToNo: this.payloadStatus.ReturnedToNo,
          Comments: this.payloadStatus.Comments,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.clearpayloadStatus();
          this.$refs.MySnackBar.showSuccessMessage("Status succesfully updated.");
        })
        .catch((err) => {
          console.error(err);
          if (err.response.status === 422) {
            this.$refs.MySnackBar.showErrorMessage("Please fill up required fields");
          } else {
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
          }
        });
      this.Modal = false;
      this.btnLoading = false;
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
          id: this.payloadStatus.id,
          status: this.payloadStatus.Status_Remarks,
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.clearpayloadStatus();
          this.$refs.MySnackBar.showSuccessMessage("Status succesfully updated.");
        })
        .catch((err) => {
          console.error(err);
          if (err.response.status === 422) {
            this.$refs.MySnackBar.showErrorMessage("Please fill up required fields");
          } else {
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
          }
        });
      this.Modal = false;
      this.btnLoading = false;
    },

    selectUser(item) {
      this.payload.emp_no = item.emp_no;
      this.payload.Name_of_User = item.full_name;
      this.payload.Department = item.DeptDesc;
      this.payload.Division = item.DivDesc;
      this.dialogList = false;
    },

    selectReturnedTo(item) {
      this.payloadStatus.ReturnedToNo = item.emp_no;
      this.payloadStatus.ReturnedTo = item.full_name;
      this.dialogList = false;
    },

    OpenDialogAdd() {
      this.payload.dateRcvd = format(new Date(), "yyyy-MM-dd HH:mm:ss");
      this.payload.dateRcvdView = format(
        new Date(this.payload.dateRcvd),
        "MMMM dd, yyyy h:mm a"
      );
      this.dialog = true;
    },

    OpenDialog(item) {
      this.payload.id = item.ID;
      this.payload.dateRcvd = item.DateReceived;
      this.payload.dateRcvdView = format(
        new Date(item.DateReceived),
        "MMMM dd, yyyy h:mm a"
      );
      this.payload.Name_of_User = item.Name_of_User;
      this.payload.emp_no = item.emp_no;
      this.payload.Department = item.DeptDesc;
      this.payload.Division = item.DivDesc;
      this.payload.ContactNo = item.contactno;
      this.payload.Device = item.Device;
      this.payload.BrandModel = item.Brand_and_Model;
      this.payload.InitialProblemsEncountered = item.InitialProblemsEncountered;
      this.payload.ProblemsEncountred = item.ProblemsEncountred;
      this.payload.assignedto = item.AssignedTo_emp_no;
      this.payload.OtherInfo = item.OtherDevInfo;
      this.dialog = true;
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

    formatDateTable(date) {
      return format(date, "MMM dd, yyyy hh:mm a");
    },

    formatDate(date) {
      return format(date, "MMM-dd-yyyy");
    },

    fetch(paramType = null) {
      let myParameter = {
        page: 1,
        searchkey: "",
        assignedFilter: this.assignedFilter,
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCRepairs/fetchCurrent",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("xxx"),
          },
          params: myParameter,
        })
          .then((resp) => {
            this.items = resp.data.data.data;
            this.myPagination.total = resp.data.data.last_page;
            this.myPagination.per_page = resp.data.data.per_page;
            this.totalRecords = resp.data.data.total;
            this.empISU = resp.data.empISU || "";
            this.fetchLoading = false;
          })
          .catch((err) => {
            this.fetchLoading = false;
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!", err);
          });
      };

      if (paramType == null) {
        myParameter = {
          page: 1,
          searchkey: this.searchkey,
          assignedFilter: this.assignedFilter,
        };
        loadData();
      } else if (paramType == "page") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
          assignedFilter: this.assignedFilter,
        };
        loadData();
      } else if (paramType == "search") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
          assignedFilter: this.assignedFilter,
        };
        loadData();
      }
    },

    fetchStatus() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCRepairs/fetchStatus",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.status = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
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

    fetchUsers() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCRepairs/fetchUsers",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.users = resp.data;

          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
    },

    fetchActionsTaken() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCRepairs/fetchActionsTaken",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        params: {
          id: this.payload.id,
        },
      })
        .then((resp) => {
          this.actionsTaken = resp.data;

          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
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

    setDateTimeAndCloseStatus() {
      if (this.datestatus && this.timestatus) {
        this.payloadStatus.DateReturned =
          format(this.datestatus, "yyyy-MM-dd") + " " + this.timestatus;
        this.payloadStatus.DateReturnedView = format(
          new Date(this.payloadStatus.DateReturned),
          "MMMM dd, yyyy h:mm a"
        );
        this.dateMenu = false;
      }
      console.log(this.payloadStatus);
    },

    OpenModal(item) {
      this.Modal = true;
      this.payload.id = item.ID;
    },

    OpenModalActionsTaken(item) {
      this.payload.id = item.ID;
      this.fetchActionsTaken();
      this.dialogActions = true;
    },

    OpenModalStatus(item) {
      this.payloadStatus.id = item.ID;
      this.payloadStatus.Status_Remarks = item.RepairStatusID;
      this.fetchStatus();
      this.dialogStatus = true;
    },

    CheckStatus() {
      console.log("payload id:", this.payload.id);
      console.log(this.payloadStatus);
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

    OpenDialogReturnedTo(item) {
      this.payloadStatus.CheckStatus = true;
      this.dialogList = true;
    },

    clearInputs() {
      this.payload.id = "";
      this.payload.dateRcvd = format(now, "yyyy-MM-dd HH:mm:ss");
      this.payload.dateRcvdView = format(now, "MMMM dd, yyyy h:mm a");
      this.payload.Name_of_User = "";
      this.payload.Department = "";
      this.payload.Division = "";
      this.payload.ContactNo = "";
      this.payload.Device = "";
      this.payload.BrandModel = "";
      this.payload.InitialProblemsEncountered = "";
      this.payload.ProblemsEncountred = "";
      this.payload.assignedto = "";
      this.payload.OtherInfo = "";
      this.payload.emp_no = "";
      this.date = now.toISOString().split("T")[0];
      this.time = now.toTimeString().split(" ")[0];
      this.dialog = false;
    },

    clearpayloadStatus() {
      this.payloadStatus.id = "";
      this.payloadStatus.Status_Remarks = "";
      this.payloadStatus.ReturnedTo = "";
      this.payloadStatus.DateReturned = format(now, "yyyy-MM-dd HH:mm:ss");
      this.payloadStatus.DateReturnedView = format(now, "MMMM dd, yyyy h:mm a");
      this.payloadStatus.Comments = "";
      this.datestatus = now.toISOString().split("T")[0];
      this.timestatus = now.toTimeString().split(" ")[0];
      this.payloadStatus.ReturnedToNo = "";
      this.payloadStatus.CheckStatus = false;
      this.dialogStatus = false;
    },
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
  width: 100%;
  position: relative;
  /* Hide scrollbar for Chrome, Safari and Opera */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE and Edge */
}
.table-responsive::-webkit-scrollbar {
  display: none; /* Chrome, Safari and Opera */
}

.table-scroll-top {
  overflow-x: auto;
  overflow-y: hidden;
  height: 16px;
  width: 100%;
  position: sticky;
  top: 0;
  z-index: 10;
  background: #fff;
  border-bottom: 1px solid #e0e0e0;
}

.table-scroll-top .scroll-content {
  width: 2500px; /* Match the table's min-width */
  height: 1px;
}

thead th {
  vertical-align: middle;
  background-color: #303847;
  color: white;
}

.date-picker-field {
  min-width: 200px;
  overflow: visible;
}

.v-divider {
  background-color: #e0e0e0;
  height: 2px;
  margin: 5px 0;
}

.actions-taken-table thead th {
  background-color: unset !important;
  color: unset !important;
}

.actions-taken-table th,
.actions-taken-table td {
  border: 1px solid #bdbdbd;
}
</style>
