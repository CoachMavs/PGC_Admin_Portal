<template>
  <h1 class="card-header pb-3">Upcoming Meetings</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
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
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!items || items.length === 0">
              <td colspan="13" class="text-center">
                No Upcoming Zoom Meetings Scheduled
              </td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td>{{ item.Employee }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.topics }}</td>
              <td>{{ formatDate(item.start_datetime) }}</td>
              <td>{{ formatDate(item.end_datetime) }}</td>
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
              <td>
                <v-tooltip text="Edit link" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      class="mr-1"
                      icon="mdi-link-edit"
                      size="small"
                      color="#14727a"
                      flat
                      @click="SetLink(item)"
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
      <v-card-title class="custom-title"> Set Zoom Link </v-card-title>
      <v-card-text color="red">
        <v-form v-model="form" ref="form">
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="payload.zoomLink"
                  :rules="[required]"
                  label="Link"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  multi-line="true"
                  rows="3"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="payload.zoomID"
                  :rules="[required]"
                  label="Meeting ID and Passcode"
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  multi-line="true"
                  rows="3"
                ></v-textarea>
              </v-col>
            </v-row>
          </v-container>

          <!-- Divider -->
          <v-divider></v-divider>
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="clearInputs(), (dialog = false)"
        >
          Close
        </v-btn>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="updateZoomLink()"
          :loading="btnLoading"
          type="submit"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import "bootstrap";
import { format } from "date-fns";
import MySnackBar from "@/components/MySnackBar.vue";
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
    channel: null,

    payload: {
      id: "",
      zoomLink: "",
      zoomID: "",
    },
    dialog: false,
    form: false,
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

    this.channel = echo.channel("portal-notifications").listen("PortalNotification", (e) => {
      if (e.message === "triggerZoomUpcoming") {
        this.fetch();
      }
    });

    //  this.intervalId = setInterval(() => {
    //   const now = new Date();

    //   this.items.forEach((item) => {
    //     const targetTime = new Date(item.start_datetime);

    //     if (targetTime < now) {
    //       this.fetchNotif();
    //     }
    //   });
    // }, 1000);
  },

  beforeUnmount() {
    if (this.channel) {
      this.channel.stopListening("PortalNotification");
    }
    // clearInterval(intervalId); // cleanup when component is destroyed
  },

  methods: {
    fetchNotif() {
      return;
    },

    clearInputs() {
      this.payload = {
        id: "",
        emp_no: "",
        zoomLink: "",
        zoomID: "",
      };
    },

    SetLink(item) {
      this.payload.id = item.id;
      this.payload.zoomLink = item.zoomlink;
      this.payload.zoomID = item.MeetingID;
      this.dialog = true;
    },

    updateZoomLink() {
      this.btnLoading = true;
      if (!this.$refs.form.validate()) {
        return;
      }
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCZoom/setZoomLink",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.id,
          zoomLink: this.payload.zoomLink,
          zoomID: this.payload.zoomID,
        },
      })
        .then((resp) => {
          this.fetch();
          this.$refs.MySnackBar.showSuccessMessage("Save successfully!");
          this.dialog = false;
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

    formatDate(date) {
      return format(new Date(date), "MMM-dd-yyyy h:mm a");
    },

    extractLink(text) {
      const urlPattern = /(https:\/\/[^\s]+)/g;
      const match = text.match(urlPattern);
      return match ? match[0] : "";
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
        url: process.env.VUE_APP_API + "PGCZoom/fetchUpcoming",
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

.custom-title {
  background-color: #14727a;
  color: white;
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
</style>
