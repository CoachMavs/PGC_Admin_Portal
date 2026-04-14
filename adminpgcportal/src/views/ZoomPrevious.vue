<template>
  <h1 class="card-header pb-3">Previous Meetings</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <v-row rows="auto">
          <v-col cols="12" sm="8" md="8">
            <v-text-field
              v-model="searchkey"
              label="Search"
              append-inner-icon="mdi-magnify"
              clearable
              @update:model-value="handleSearchInput"
            />
          </v-col>

          <v-col cols="12" sm="4" md="4">
            <v-text-field
              v-model="totalRecords"
              label="Loaded record(s):"
              append-inner-icon="mdi-counter"
              readonly
            />
          </v-col>
        </v-row>
      </div>

      <div class="table-responsive" ref="tableScrollContainer">
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
import echo from "./echo";

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
    totalRecords: 0,
    hasMore: true,
    observer: null,

    myPagination: {
      page: 1,
      per_page: 0,
    },
  }),

  mounted() {
    this.fetch();
    this.setupInfiniteScroll();

    this.channel = echo.channel("portal-notifications").listen("PortalNotification", (e) => {
      if (e.message === "triggerZoomPrev") {
        this.fetch();
      }
    });
  },

  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }

    if (this.channel) {
      this.channel.stopListening("PortalNotification");
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
          root: this.$refs.tableScrollContainer || null,
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
    extractLink(text) {
      const urlPattern = /(https:\/\/[^\s]+)/g;
      const match = text.match(urlPattern);
      return match ? match[0] : "";
    },
    fetch(paramType = null) {
      let myParameter = {
        page: 1,
        searchkey: "",
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
          url: process.env.VUE_APP_API + "PGCZoom/fetchPrev",
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
        };
        loadData();
      } else if (paramType == "page") {
        myParameter = {
          page: this.myPagination.page,
          searchkey: this.searchkey,
        };
        loadData();
      } else if (paramType == "search") {
        myParameter = {
          page: 1,
          searchkey: this.searchkey,
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
  max-height: calc(100vh - 260px);
  position: relative;
}

table {
  border-collapse: separate;
  border-spacing: 0;
}

.v-divider {
  background-color: #e0e0e0;
  height: 2px;
  margin: 5px 0;
}

thead th {
  position: sticky;
  top: 0;
  z-index: 5;
  vertical-align: middle;
  background-color: #303847;
  color: white;
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
