<template>
  <h1 class="card-header pb-3">Issued Waste Certification</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>

    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <v-row rows="auto">
          <v-col cols="12" md="7">
            <v-text-field v-model="searchkey" label="Search" append-inner-icon="mdi-magnify" clearable
              @update:model-value="handleSearchInput" />
          </v-col>

          <v-col cols="12" md="2">
            <v-select v-model="assignedFilter" :items="['All', 'Only me']" append-inner-icon="mdi-filter-outline"
              label="Assigned to:" dense @update:model-value="handleSearchInput" />
          </v-col>

          <v-col cols="12" md="2">
            <v-text-field v-model="totalRecords" label="Total record(s) found:" append-inner-icon="mdi-counter"
              readonly />
          </v-col>

          <v-col cols="12" md="1">
            <div class="text-center">
              <v-btn color="#14727a" @click="OpenDialogAdd()" style="height: 55px; width: 100%" block>
                <div class="d-flex flex-column align-start">
                  <span>Add</span>
                </div>
                <v-icon class="ml-2">mdi-newspaper-plus</v-icon>
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Top Scrollbar -->
      <!-- <div class="table-scroll-top" ref="tableScrollTop">
      <div class="scroll-content"></div>
    </div> -->

      <!-- Table with Bottom Scrollbar -->

      <div class="table-responsive" style="white-space: auto" ref="tableScrollBottom">
        <div style="height: 12px"></div>

        <table class="table">
          <thead>
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Reference No.</th>
              <th scope="col">Certificate No.</th>
              <th scope="col">Name of User</th>
              <th scope="col">Department</th>
              <th scope="col">Division</th>
              <th scope="col">Type of Device</th>
              <th scope="col">Brand and Model</th>
              <th scope="col">Issued Date</th>
              <th scope="col">Diagnosis</th>
              <th scope="col">Recommendation</th>
              <th scope="col">Assigned Tech</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td style="width: 40px; word-break: break-word; white-space: normal">
                <v-tooltip text="Edit" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" class="mr-1 mb-1" icon="mdi-note-edit-outline" size="small" color="#14727a"
                      flat @click="OpenEditDialog(item)">
                    </v-btn>
                  </template>
                </v-tooltip>

                <!-- <v-tooltip text="Delete" location="top">
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
                </v-tooltip> -->

                <v-tooltip text="Print" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" class="mr-1 mb-1" icon="mdi-printer-outline" size="small" color="#14727a" flat
                      @click="exportToPDF(item)">
                    </v-btn>
                  </template>
                </v-tooltip>
              </td>
              <td>{{ item.ReferenceNo1 }}</td>
              <td>{{ item.ReferenceNo }}</td>
              <td>{{ item.Name_of_User }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.Device }}</td>
              <td>{{ item.Brand_and_Model }}</td>
              <td style="width: 120px; word-break: break-word; white-space: normal">
                <div v-html="formatDateTable(item.DDate)"></div>
              </td>
              <td style="width: 600px; word-break: break-word; white-space: normal">
                {{ item.Diagnosis }}
              </td>
              <td style="width: 600px; word-break: break-word; white-space: normal">
                {{ item.Recommendation }}
              </td>
              <td>{{ item.AssignedTo }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center">
      <v-pagination v-model="myPagination.page" :length="myPagination.total"
        :total-visible="$vuetify.display.smAndDown ? 1 : 7" :size="$vuetify.display.smAndDown ? 'small' : 'default'"
        @update:model-value="fetch('page')" rounded="circle" color="#673AB7" class="my-pagination"></v-pagination>
    </div>
  </div>

  <!-- Modal Add Edit -->
  <v-dialog v-model="dialog" persistent width="700">
    <v-card>
      <v-card-title class="custom-title"> Add For Waste Certification </v-card-title>

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
              <v-col :md="AE ? 6 : 12" cols="12">
                <v-text-field v-model="payloadExtra.Name_of_User" label="Name of User" color="#14727a"
                  variant="outlined" hide-details="auto" readonly>
                  <template v-slot:append-inner>
                    <v-icon @click="OpenDialogList()" class="cursor-pointer">mdi-magnify</v-icon>
                  </template>
                </v-text-field>
              </v-col>

              <v-col cols="12" md="6" v-if="AE">
                <v-text-field v-model="payloadExtra.assignedto" item-value="emp_no" item-title="empISU"
                  label="Assigned To" color="#14727a" variant="outlined" hide-details="auto" readonly />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="payloadExtra.Department" label="Department" color="#14727a" variant="outlined"
                  hide-details="auto" readonly />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="payloadExtra.Division" label="Division" color="#14727a" variant="outlined"
                  hide-details="auto" readonly />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="payloadExtra.Device" label="Device" color="#14727a"
                  variant="outlined" hide-details="auto" readonly />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="payloadExtra.BrandModel" :placeholder="'ex: EPSON L3110'" label="Brand/Model"
                  color="#14727a" variant="outlined" hide-details="auto" readonly />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-menu v-model="menuDDate" :close-on-content-click="false" transition="scale-transition"
                  min-width="auto">
                  <template v-slot:activator="{ props }">
                    <v-text-field :model-value="formatDialogDate(payload.DDate)" label="Date Issued"
                      append-inner-icon="mdi-calendar" color="#14727a" variant="outlined" hide-details="auto" readonly
                      v-bind="props" :rules="[required]" validate-on="blur" />
                  </template>

                  <v-card class="d-flex justify-center align-center" style="width: 320px">
                    <v-date-picker :model-value="payload.DDate" color="#14727a" hide-header @update:model-value="
                      (value) => {
                        payload.DDate = value;
                        menuDDate = false;
                      }
                    "></v-date-picker>
                  </v-card>
                </v-menu>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-textarea v-model="payload.Diagnosis" label="Diagnosis" color="#14727a" variant="outlined"
                  hide-details="auto" rows="1" required :rules="[required]" auto-grow validate-on="blur" />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-textarea v-model="payload.Recommendation" label="Recommendation" color="#14727a" variant="outlined"
                  hide-details="auto" rows="1" required :rules="[required]" auto-grow validate-on="blur" />
              </v-col>
            </v-row>
          </v-container>
          <v-divider></v-divider>
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="#14727a" variant="elevated" style="text-transform: none" @click="clearInputs()">
          Close
        </v-btn>
        <v-btn color="#14727a" variant="elevated" style="text-transform: none" @click="addPre()" :loading="btnLoading"
          type="submit">
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- End of Modal -->

  <!-- Modal with Data Table -->
  <v-dialog v-model="dialogList" max-width="800px">
    <v-card>
      <v-card-title style="background-color: #14727a; color: white" class="d-flex align-center justify-space-between">
        <span>Select Record</span>
        <v-btn icon variant="text" style="background: transparent" @click="dialogList = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-text-field v-model="userSearch" label="Search user" clearable class="mb-3"
          prepend-inner-icon="mdi-magnify" />
        <v-data-table class="my-table elevation-1" header-class="my-table-header" v-if="filteredUsers.length"
          :headers="headers" :items="filteredUsers" item-value="ID">
          <template v-slot:item="{ item }">
            <tr @click="selectUser(item)" style="cursor: pointer">
              <td>{{ item.ReferenceNo }}</td>
              <td>{{ item.Name_of_User }}</td>
              <td>{{ item.Type_of_Device }}</td>
              <td>{{ item.Brand_and_Model }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.AssignedTo }}</td>
            </tr>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-dialog>

  <!-- End of Modal -->

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
        <v-btn color="#14727a" variant="elevated" style="text-transform: none" @click="
          Modal = false;
        payload.id = '';
        ">
          Cancel
        </v-btn>
        <v-btn color="#14727a" variant="elevated" style="text-transform: none" @click="DeleteReq()"
          :loading="btnLoading">
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
import pdfMake from "pdfmake/build/pdfmake";
import "@/pdfmake-times-vfs.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import echo from "./echo";

export default {
  name: "ZoomPrevious",
  components: {
    MySnackBar,
  },
  data: () => ({
    menuDDate: false,
    fetchLoading: false,
    btnLoading: false,
    form: false,
    items: [],
    users: [],
    Modal: false,
    dialog: false,
    dialogList: false,
    searchkey: "",
    assignedFilter: "All",
    userSearch: "",
    totalRecords: 0,
    AE: "",

    headers: [
      { title: "Reference No.", key: "ReferenceNo" },
      { title: "User", key: "Employee" },
      { title: "Device", key: "Type_of_Device" },
      { title: "Brand & Model", key: "Brand_and_Model" },
      { title: "Department", key: "DeptDesc" },
      { title: "Tech", key: "AssignedTo" },
    ],

    payload: {
      id: "",
      Diagnosis: "",
      Recommendation: "",
      repairlogID: "",
      DDate: "",
    },

    payloadExtra: {
      assignedto: "",
      Name_of_User: "",
      Department: "",
      Division: "",
      Device: "",
      BrandModel: "",
    },

    myPagination: {
      page: 1,
      total: 10,
      per_page: 0,
    },
  }),

  computed: {
    filteredUsers() {
      if (!this.userSearch) return this.users;

      const search = this.userSearch.toLowerCase();

      return this.users.filter((u) => {
        return (
          (u.ReferenceNo && u.ReferenceNo.toLowerCase().includes(search)) ||
          (u.AssignedTo && u.AssignedTo.toString().toLowerCase().includes(search)) ||
          (u.Name_of_User && u.Name_of_User.toString().toLowerCase().includes(search)) ||
          (u.Type_of_Device && u.Type_of_Device.toLowerCase().includes(search)) ||
          (u.Brand_and_Model && u.Brand_and_Model.toLowerCase().includes(search))
        );
      });
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
    this.fetch();

    this.channel = echo.channel("portal-notifications").listen("PortalNotification", (e) => {
      if (e.message === "triggerWasteCertificate") {
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

    formatDateWithSuffix(date) {
      const day = date.getDate();
      const suffix =
        day === 1 || day === 21 || day === 31
          ? "st"
          : day === 2 || day === 22
            ? "nd"
            : day === 3 || day === 23
              ? "rd"
              : "th";

      const month = date.toLocaleString("en-US", { month: "long" });
      const year = date.getFullYear();

      return `${day}${suffix} of ${month}, ${year}`;
    },

    formatIssuedDate(date) {
      const day = date.getDate();
      const suffix =
        day === 1 || day === 21 || day === 31
          ? "st"
          : day === 2 || day === 22
            ? "nd"
            : day === 3 || day === 23
              ? "rd"
              : "th";
      const month = date.toLocaleString("en-US", { month: "long" });
      const year = date.getFullYear();
      return `Issued this ${day}${suffix} day of ${month}, ${year}.`;
    },

    loadImageAsBase64(url) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = "Anonymous";
        img.onload = () => {
          const canvas = document.createElement("canvas");
          canvas.width = img.width;
          canvas.height = img.height;
          const ctx = canvas.getContext("2d");
          ctx.drawImage(img, 0, 0);
          const base64 = canvas.toDataURL("image/png");
          resolve(base64);
        };
        img.onerror = reject;
        img.src = url;
      });
    },

    async exportToPDF(item) {
      const logoBase64 = await this.loadImageAsBase64("/pgc.png");
      const logoBase641 = await this.loadImageAsBase64("/onecagayan.png");

      const docDefinition = {
        pageSize: "A4",
        pageMargins: [20, 0, 20, 0],
        defaultStyle: {
          font: "TimesNewRoman",
        },
        content: [
          {
            table: {
              widths: ["15%", "70%", "15%"],
              body: [
                [
                  {
                    image: logoBase64,
                    width: 80,
                    alignment: "left",
                    margin: [0, 15, 0, 0],
                  },
                  {
                    stack: [
                      {
                        text: "Republic of the Philippines",
                        style: "header",
                        lineHeight: 1.2,
                      },
                      {
                        text: "PROVINCE OF CAGAYAN",
                        style: "header",
                        bold: true,
                        lineHeight: 1.2,
                      },
                      {
                        text: "Tuguegarao City, Cagayan",
                        style: "header",
                        lineHeight: 1.2,
                      },
                      {
                        text: "PROVINCIAL ADMINISTRATOR'S OFFICE",
                        style: "header",
                        fontSize: 14,
                        bold: true,
                        lineHeight: 1.2,
                        alignment: "center",
                      },
                      {
                        text: "INFORMATION SYSTEMS DIVISION",
                        style: "header",
                        lineHeight: 1.2,
                      },
                    ],
                    alignment: "center",
                    margin: [0, 23, 0, 0],
                  },
                  {
                    image: logoBase641,
                    width: 80,
                    alignment: "right",
                    margin: [0, 15, 0, 0],
                  },
                ],
              ],
            },
            layout: {
              paddingTop: () => 0,
              paddingBottom: () => 0,
              paddingLeft: () => 0,
              paddingRight: () => 0,
              hLineWidth: () => 0,
              vLineWidth: () => 0,
            },
            margin: [0, 0, 0, 0],
          },
          {
            canvas: [{ type: "line", x1: 0, y1: 0, x2: 555, y2: 0, lineWidth: 2 }],
            margin: [0, 0, 40, 0],
          },
          {
            stack: [
              {
                columns: [
                  {
                    width: "auto",
                    qr: `${item.ReferenceNo1 ?? ""}`,
                    fit: 50,
                    alignment: "left",
                  },
                  {
                    width: "*",
                    text: "",
                  },
                  {
                    width: "auto",
                    text: [
                      { text: "Reference No.: ", bold: false },
                      { text: item.ReferenceNo, bold: true },
                    ],
                    alignment: "right",
                    margin: [0, 0, 0, 0],
                  },
                ],
                margin: [0, 0, 0, 40],
                columnGap: 10,
              },
              {
                text: "C  E  R  T  I  F  I  C  A  T  I  O  N",
                alignment: "center",
                decoration: "underline",
                bold: true,
                fontSize: 16,
                margin: [0, 0, 0, 20],
              },
              {
                stack: [
                  {
                    text: "To Whom It May Concern:",
                    margin: [0, 0, 0, 10],
                  },
                  {
                    text: [
                      "This is to certify that upon diagnostics and checking conducted by this office, the ",
                      { text: item.Brand_and_Model, bold: true },
                      " ",
                      { text: item.Device, bold: true },
                      " issued to ",
                      { text: item.Name_of_User, bold: true },
                      " of the ",
                      { text: item.DeptDesc, bold: true },
                      " has been found to be ",
                      { text: item.Diagnosis, bold: true },
                      ". It is further recommended that the said ICT Equipment/Device be ",
                      { text: item.Recommendation, bold: true },
                      ".",
                    ],
                    alignment: "justify",
                    margin: [0, 0, 0, 20],
                  },

                  {
                    text: this.formatIssuedDate(new Date(item.DDate)),
                    margin: [0, 0, 0, 50],
                  },
                ],
                fontSize: 12, // Apply font size here for this block
              },
              {
                text: "Inspected by:",
                margin: [0, 0, 0, 30],
              },
              {
                text: `${item.empISU}`,
                margin: [0, 0, 0, 0],
                bold: true,
              },
              {
                text: "Technician in Charge",
                margin: [0, 0, 0, 20],
              },
              { text: "Noted by:", margin: [0, 0, 0, 30] },
              {
                text: "IRWIN C. CANSEJO",
                bold: true,
              },
              {
                text: "Information Systems Analyst III",
              },
            ],
            margin: [40, 20, 40, 0],
            fontSize: 11,
          },
        ],
        styles: {
          header: {
            fontSize: 12,
            alignment: "center",
          },
          body: {
            fontSize: 25,
            alignment: "justify",
          },
        },
      };

      pdfMake.createPdf(docDefinition).open();
    },

    OpenDialogList() {
      this.fetchUsers();
      this.dialogList = true;
    },

    addPre() {
      this.btnLoading = true;
      if (!this.$refs.form.validate()) {
        return;
      }
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCCertWaste/addPRE",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          ...this.payload,
          DDate: this.formatPayloadDate(this.payload.DDate),
        },
      })
        .then((resp) => {
          // this.fetch();
          this.fetchNotif();
          this.clearInputs();
          this.fetch();
          this.$refs.MySnackBar.showSuccessMessage(
            "Pre-certification records succesfully updated."
          );
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

    fetchUsers() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "PGCCertWaste/fetchRequest",
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

    selectUser(item) {
      this.payload.id = item.ID;
      this.payloadExtra.assignedto = item.AssignedTo;
      this.payloadExtra.Name_of_User = item.Name_of_User;
      this.payloadExtra.Department = item.DeptDesc;
      this.payloadExtra.Division = item.DivDesc;
      this.payloadExtra.Device = item.Type_of_Device;
      this.payloadExtra.BrandModel = item.Brand_and_Model;
      this.payload.repairlogID = item.repairlogID;
      this.payload.Diagnosis = "DEFECTIVE. Initial troubleshooting was conducted, and it was determined that the " + item.Type_of_Device?.toLowerCase() + " is defective; however, the issue remains unresolved.";
      this.payload.Recommendation = "considered beyond repair and is recommended for proper retirement or replacement";
      this.payload.DDate = this.payload.DDate || format(new Date(), "yyyy-MM-dd");
      this.dialogList = false;

      console.log("repairlogID:", this.payload.repairlogID);

      this.$nextTick(() => {
        if (this.$refs.referenceNo && this.$refs.referenceNo.validate) {
          this.$refs.referenceNo.validate();
        }
      });
    },

    OpenDialogAdd() {
      this.AE = "";
      this.payload.DDate = format(new Date(), "yyyy-MM-dd");
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
    formatDialogDate(date) {
      if (!date) return "";
      return format(new Date(date), "MMM dd, yyyy");
    },
    formatPayloadDate(date) {
      if (!date) return "";
      return format(new Date(date), "yyyy-MM-dd");
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
          url: process.env.VUE_APP_API + "PGCCertWaste/fetchWaste",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("xxx"),
          },
          params: myParameter,
        })
          .then((resp) => {
            this.items = resp.data.data;
            this.myPagination.total = resp.data.last_page;
            this.myPagination.per_page = resp.data.per_page;
            this.totalRecords = resp.data.total;
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

    DeleteReq() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCCertWaste/DeleteReq",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.id,
        },
      })
        .then((resp) => {
          this.payload.id = "";
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

    OpenModal(item) {
      this.Modal = true;
      this.payload.id = item.ID;
      console.log("ID: ", this.payload.id);
    },

    OpenEditDialog(item) {
      this.payloadExtra.assignedto = item.AssignedTo;
      this.payloadExtra.Name_of_User = item.Name_of_User;
      this.payloadExtra.Department = item.DeptDesc;
      this.payloadExtra.Division = item.DivDesc;
      this.payloadExtra.Device = item.Device;
      this.payloadExtra.BrandModel = item.Brand_and_Model;

      this.payload.id = item.ID;
      this.payload.Recommendation = item.Recommendation;
      this.payload.Diagnosis = item.Diagnosis;
      this.payload.repairlogID = item.repairlogID;
      this.payload.DDate = item.DDate ? format(new Date(item.DDate), "yyyy-MM-dd") : format(new Date(), "yyyy-MM-dd");

      this.AE = 1;

      console.log("repairlogID: ", this.payload.repairlogID);
      this.dialog = true;
    },

    clearInputs() {
      this.payload.id = "";
      this.payload.Recommendation = "";
      this.payload.Diagnosis = "";
      this.payload.repairlogID = "";
      this.payload.DDate = "";

      this.payloadExtra.assignedto = "";
      this.payloadExtra.Name_of_User = "";
      this.payloadExtra.Department = "";
      this.payloadExtra.Division = "";
      this.payloadExtra.Device = "";
      this.payloadExtra.BrandModel = "";

      this.AE = "";

      this.dialog = false;
    },
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
  /* Hide the bottom horizontal scrollbar */
}

/* Remove .table-scroll-top styles */
/*
.table-scroll-top {
  overflow-x: auto; 
  overflow-y: hidden;
  margin-bottom: -5px; 
  height: 16px; 
  position: relative; 
}

.table-scroll-top .scroll-content {
  width: 2500px; 
  height: 1px; 
}
*/

thead th {
  vertical-align: middle;
  background-color: #303847;
  color: white;
}

.date-picker-field {
  min-width: 200px;
  /* Adjust width as needed */
  overflow: visible;
  /* Ensure text is not clipped */
}

.v-divider {
  background-color: #e0e0e0;
  height: 2px;
  margin: 5px 0;
}
</style>
