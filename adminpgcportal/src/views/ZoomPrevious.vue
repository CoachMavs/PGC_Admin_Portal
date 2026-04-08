<template>
  <h1 class="card-header pb-3">Previous Meetings</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <v-row rows="auto">
          <v-col cols="12" sm="6" md="4">
            <v-text-field
              v-model="searchkey"
              label="Search"
              append-inner-icon="mdi-magnify"
              clearable
              @update:model-value="handleSearchInput"
            />
          </v-col>

          <!-- Date From -->
          <v-col cols="12" sm="6" md="3">
            <v-menu
              v-model="menufrom"
              :close-on-content-click="false"
              transition="scale-transition"
              min-width="auto"
            >
              <template v-slot:activator="{ props }">
                <v-text-field
                  v-model="formattedDateFrom"
                  label="From"
                  append-inner-icon="mdi-calendar"
                  readonly
                  v-bind="props"
                  class="date-picker-field"
                  @input="handleDateInput"
                ></v-text-field>
              </template>

              <v-card class="d-flex justify-center align-center" style="width: 320px">
                <v-date-picker
                  v-model="datefrom"
                  hide-header
                  @update:model-value="
                    (value) => {
                      datefrom = value;
                      updateFormattedDateFrom();
                      fetch(); // Trigger fetch after updating the date
                      menufrom = false; // Close the menu after selecting a date
                    }
                  "
                ></v-date-picker>
              </v-card>
            </v-menu>
          </v-col>

          <!-- Date To -->
          <v-col cols="12" sm="6" md="3">
            <v-menu
              v-model="menuto"
              :close-on-content-click="false"
              transition="scale-transition"
              min-width="auto"
            >
              <template v-slot:activator="{ props }">
                <v-text-field
                  style="justify-content: center"
                  v-model="formattedDateTo"
                  label="To"
                  append-inner-icon="mdi-calendar"
                  readonly
                  v-bind="props"
                  class="date-picker-field"
                ></v-text-field>
              </template>

              <v-card class="d-flex justify-center align-center" style="width: 320px">
                <v-date-picker
                  v-model="dateto"
                  hide-header
                  @update:model-value="
                    (value) => {
                      dateto = value;
                      updateFormattedDateTo();
                      fetch();
                      menuto = false; // Close the menu after selecting a date
                    }
                  "
                ></v-date-picker>
              </v-card>
            </v-menu>
          </v-col>

          <v-col cols="12" sm="6" md="2">
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
          <thead class="custom-title">
            <tr>
              <th scope="col">Requestor</th>
              <th scope="col">Office</th>
              <th scope="col">Division</th>
              <th scope="col">Topic</th>
              <th scope="col">Start</th>
              <th scope="col">End</th>
              <th scope="col">Participants</th>
              <th scope="col">Assigned to</th>
              <th scope="col">Zoom Account</th>
              <th scope="col">Zoom Link</th>
              <th scope="col">Meeting ID / Passcode</th>
              <th scope="col">Contact</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.Employee }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.topics }}</td>
              <td>{{ formatDateTable(item.start_datetime) }}</td>
              <td>{{ formatDateTable(item.end_datetime) }}</td>
              <td>{{ item.noofparticipants }}</td>
              <td>{{ item.assignedto }}</td>
              <td>{{ item.zoomaccount }}</td>
              <td style="max-width: 300px">
                <a :href="extractLink(item.zoomlink)" target="_blank">{{
                  extractLink(item.zoomlink)
                }}</a>
              </td>
              <td>{{ item.MeetingID }}</td>
              <td>{{ item.contact }}</td>
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

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */

import axios from "axios";
import "bootstrap";
import { format } from "date-fns";
import MySnackBar from "@/components/MySnackBar.vue";
import { ref, onMounted, onBeforeUnmount } from "vue";

export default {
  name: "ZoomPrevious",
  components: {
    MySnackBar,
  },
  data: () => ({
    datefrom: null, // Initialize as null
    dateto: null, // Initialize as null
    formattedDateFrom: "",
    formattedDateTo: "",

    menufrom: false,
    menuto: false,
    fetchLoading: false,
    btnLoading: false,
    items: [],
    Modal: false,
    dialog: false,
    searchkey: "",
    totalRecords: 0,

    myPagination: {
      page: 1,
      total: 5,
      per_page: 0,
    },
  }),

  mounted() {
    this.dateto = new Date();
    this.formattedDateTo = this.formatDate(this.dateto);

    // Set "Date From" as 15 days before today
    let pastDate = new Date();
    pastDate.setDate(pastDate.getDate() - 15); // Subtract 15 days
    this.datefrom = pastDate;
    this.formattedDateFrom = this.formatDate(this.datefrom);

    this.fetch();

    this.channel = echo.channel("chat").listen(".message.sent", (e) => {
      if (e.message === "triggerZoomPrev") {
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
    updateFormattedDateFrom() {
      this.formattedDateFrom = this.datefrom ? this.formatDate(this.datefrom) : "";
    },
    updateFormattedDateTo() {
      this.formattedDateTo = this.dateto ? this.formatDate(this.dateto) : "";
    },
    extractLink(text) {
      const urlPattern = /(https:\/\/[^\s]+)/g;
      const match = text.match(urlPattern);
      return match ? match[0] : "";
    },
    fetch(paramType = null) {
      let myParameter = {
        page: 1,
        searchkey: "",
        datefrom: this.formatDate(this.datefrom),
        dateto: this.formatDate(this.dateto),
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCZoom/fetchPrev",
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
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
        };
        loadData();
      } else if (paramType == "page") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
        };
        loadData();
      } else if (paramType == "search") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
        };
        loadData();
      }
    },
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}

.date-picker-field {
  min-width: 200px; /* Adjust width as needed */
  overflow: visible; /* Ensure text is not clipped */
}

.v-divider {
  background-color: #e0e0e0;
  height: 2px;
  margin: 5px 0;
}

thead th {
  vertical-align: middle;
  background-color: #303847;
  color: white;
}
</style>
