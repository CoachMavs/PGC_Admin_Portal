<template>
  <h1 class="card-header pb-3">Previous Repairs</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <v-row rows="auto">
          <v-col cols="12" md="4">
            <v-text-field
              v-model="searchkey"
              label="Search"
              append-inner-icon="mdi-magnify"
              clearable
              @update:model-value="handleSearchInput"
            />
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

          <!-- Date From -->
          <v-col cols="12" md="2">
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
          <v-col cols="12" md="2">
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
              <th scope="col">Date Received</th>
              <th scope="col">Name of User</th>
              <th scope="col">Requestor</th>
              <th scope="col">Department</th>
              <th scope="col">Division</th>
              <th scope="col">Type of Device</th>
              <th scope="col">Problems Encountered</th>
              <th scope="col">Received By</th>
              <th scope="col">Assigned To</th>
              <th scope="col">Actions Taken</th>
              <th scope="col">Status</th>
              <th scope="col">Returned To</th>
              <th scope="col">Date Returned</th>
              <th scope="col">Contact No.</th>
              <th scope="col">Comment</th>
              <th scope="col">Other Device Info</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>
                <div v-html="formatDate(item.DateReceived)"></div>
              </td>

              <td>{{ item.Name_of_User }}</td>
              <td>{{ item.Employee }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.Device }}</td>
              <td style="width: 600px; word-break: break-word; white-space: normal">
                {{ item.ProblemsEncountred }}
              </td>
              <td>{{ item.Receivedby }}</td>
              <td>{{ item.AssignedTo }}</td>
              <td style="width: 1000px; word-break: break-word; white-space: normal">
                {{ item.ActionsTaken }}
              </td>
              <td>{{ item.RepairStatus }}</td>
              <td>{{ item.ReturnedTo }}</td>
              <td>
                <div v-html="formatDate(item.DateReturned)"></div>
              </td>
              <td>{{ item.contactno }}</td>
              <td>{{ item.comment }}</td>
              <td>{{ item.OtherDevInfo }}</td>
            </tr>
          </tbody>
        </table>
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
  </div>

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */

import axios from "axios";
import "bootstrap";
import { format } from "date-fns";
import MySnackBar from "@/components/MySnackBar.vue";

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
    assignedFilter: "All",
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

    let pastDate = new Date();
    let year = pastDate.getFullYear();
    this.datefrom = new Date(year, 0, 1);
    this.formattedDateFrom = this.formatDate(this.datefrom);

    this.fetch();

    const topScroll = this.$refs.tableScrollTop;
    const bottomScroll = this.$refs.tableScrollBottom;

    topScroll.addEventListener("scroll", () => {
      bottomScroll.scrollLeft = topScroll.scrollLeft;
    });

    bottomScroll.addEventListener("scroll", () => {
      topScroll.scrollLeft = bottomScroll.scrollLeft;
    });
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
        assignedFilter: this.assignedFilter,
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCRepairs/fetchPrev",
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
          assignedFilter: this.assignedFilter,
        };
        loadData();
      } else if (paramType == "page") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
          assignedFilter: this.assignedFilter,
        };
        loadData();
      } else if (paramType == "search") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
          assignedFilter: this.assignedFilter,
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
  width: 100%;
  position: relative;
  /* Hide scrollbar visually but allow scrolling */
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
</style>
