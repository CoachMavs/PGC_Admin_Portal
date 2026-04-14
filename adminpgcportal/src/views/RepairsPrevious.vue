<template>
  <h1 class="card-header pb-3">Previous Repairs</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <v-row rows="auto">
          <v-col cols="12" md="8">
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

          <v-col cols="12" md="2">
            <v-text-field
              v-model="totalRecords"
              label="Loaded record(s):"
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
        <div class="infinite-scroll-status">
          <div ref="infiniteSentinel" class="infinite-sentinel"></div>
          <v-progress-circular
            v-if="loadingMore"
            indeterminate
            color="#14727a"
            size="28"
          ></v-progress-circular>
          <div v-else-if="!hasMore && items.length" class="infinite-end">
            No more records to load
          </div>
        </div>
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
    fetchLoading: false,
    loadingMore: false,
    btnLoading: false,
    items: [],
    Modal: false,
    dialog: false,
    searchkey: "",
    assignedFilter: "All",
    totalRecords: 0,
    hasMore: true,
    observer: null,
    topScrollHandler: null,
    bottomScrollHandler: null,

    myPagination: {
      page: 1,
      total: 5,
      per_page: 0,
    },
  }),

  mounted() {
    this.fetch();

    const topScroll = this.$refs.tableScrollTop;
    const bottomScroll = this.$refs.tableScrollBottom;

    this.topScrollHandler = () => {
      bottomScroll.scrollLeft = topScroll.scrollLeft;
    };

    this.bottomScrollHandler = () => {
      topScroll.scrollLeft = bottomScroll.scrollLeft;
    };

    topScroll.addEventListener("scroll", this.topScrollHandler);
    bottomScroll.addEventListener("scroll", this.bottomScrollHandler);

    this.setupInfiniteScroll();
  },

  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }

    const topScroll = this.$refs.tableScrollTop;
    const bottomScroll = this.$refs.tableScrollBottom;

    if (topScroll && this.topScrollHandler) {
      topScroll.removeEventListener("scroll", this.topScrollHandler);
    }

    if (bottomScroll && this.bottomScrollHandler) {
      bottomScroll.removeEventListener("scroll", this.bottomScrollHandler);
    }
  },

  methods: {
    setupInfiniteScroll() {
      if (this.observer) {
        this.observer.disconnect();
      }

      const sentinel = this.$refs.infiniteSentinel;
      if (!sentinel) return;

      this.observer = new IntersectionObserver(
        (entries) => {
          if (entries[0]?.isIntersecting) {
            this.loadNextPage();
          }
        },
        {
          root: this.$refs.tableScrollBottom || null,
          rootMargin: "0px 0px 300px 0px",
          threshold: 0,
        }
      );

      this.observer.observe(sentinel);
    },
    loadNextPage() {
      if (!this.hasMore || this.fetchLoading || this.loadingMore) {
        return;
      }

      this.myPagination.page += 1;
      this.fetch("page");
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
    extractLink(text) {
      const urlPattern = /(https:\/\/[^\s]+)/g;
      const match = text.match(urlPattern);
      return match ? match[0] : "";
    },
    fetch(paramType = null) {
      let myParameter = {
        page: 1,
        searchkey: "",
        assignedFilter: this.assignedFilter,
      };

      let loadData = () => {
        const isAppending = myParameter.page > 1;

        if (isAppending) {
          this.loadingMore = true;
        } else {
          this.fetchLoading = true;
        }

        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCRepairs/fetchPrev",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("xxx"),
          },
          params: myParameter,
        })
          .then((resp) => {
            const fetchedItems = resp.data.data || [];
            this.items = isAppending ? [...this.items, ...fetchedItems] : fetchedItems;
            this.myPagination.page = resp.data.current_page;
            this.myPagination.per_page = resp.data.per_page;
            this.totalRecords = this.items.length;
            this.hasMore = !!resp.data.next_page_url;
            this.fetchLoading = false;
            this.loadingMore = false;
          })
          .catch((err) => {
            this.fetchLoading = false;
            this.loadingMore = false;
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
          page: 1,
          searchkey: this.searchkey,
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
  overflow: auto;
  width: 100%;
  max-height: calc(100vh - 290px);
  position: relative;
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

table {
  border-collapse: separate;
  border-spacing: 0;
}

thead th {
  position: sticky;
  top: 16px;
  z-index: 5;
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

.infinite-scroll-status {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 56px;
  padding: 12px 0 4px;
}

.infinite-sentinel {
  width: 1px;
  height: 1px;
}

.infinite-end {
  color: #6c757d;
  font-size: 0.95rem;
}
</style>
