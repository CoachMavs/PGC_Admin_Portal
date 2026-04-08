<template>
  <h1
    class="card-header pb-3"
    :class="[isDark ? 'bg-dark text-white' : 'bg-light text-dark']"
  >
    List of Employees
  </h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center ma-3">
        <v-row>
          <v-text-field
            v-model="searchkey"
            label="Search"
            append-inner-icon="mdi-magnify"
            clearable
            @update:model-value="handleSearchInput"
          />
        </v-row>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Employee No.</th>
              <th scope="col">Name</th>
              <th scope="col">Office</th>
              <th scope="col">Division</th>
              <th scope="col">Gender</th>
              <th scope="col">Contact No.</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.emp_no }}</td>
              <td>{{ item.fullname }}</td>
              <td>{{ item.DeptDesc }}</td>
              <td>{{ item.DivDesc }}</td>
              <td>{{ item.Sex }}</td>
              <td>{{ item.CellNo }}</td>
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
        @update:model-value="fetchEmployees('page')"
        rounded="circle"
        color="#673AB7"
        class="my-pagination"
      ></v-pagination>
    </div>
  </div>

  <!-- Modal -->
  <v-dialog v-model="Modal" persistent width="526">
    <v-card>
      <v-card-title class="custom-title"> Confirmation </v-card-title>

      <v-card-text>
        {{ `Are you sure you want to suspend ${this.idload.desc}?` }}<br />
        <br />
        {{ `Please leave a reason.` }}
      </v-card-text>

      <!-- <v-textarea class="ma-5" v-model="payload.remarks" :error-messages="rules.remarks" label="Reason"
      variant="outlined" hide-details="auto" required; multi-line="true"; rows="3"></v-textarea> -->

      <v-textarea
        v-model="payload.remarks"
        :error-messages="rules.remarks"
        label="Reason"
        variant="outlined"
        hide-details="auto"
        required
        multi-line="true"
        rows="3"
        class="pa-5"
      ></v-textarea>

      <!-- Divider -->
      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue-darken-1" variant="text" @click="Modal = false">
          Cancel
        </v-btn>
        <v-btn
          color="blue-darken-1"
          variant="text"
          @click="addLog()"
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
import MySnackBar from "@/components/MySnackBar.vue";
import { useTheme } from "vuetify";
import { computed } from "vue";

export default {
  name: "FooTer",
  components: {
    MySnackBar,
  },
  setup() {
    const theme = useTheme();
    const isDark = computed(() => theme.global.name.value === "dark");
    return {
      isDark,
    };
  },
  data: () => ({
    searchkey: "",
    selectedItem: null,
    classifics: [],
    items: [],
    classlist: [],
    Modal: false,
    fetchLoading: false,
    btnLoading: false,
    dialog: false,
    idload: {
      id: 0,
      desc: "",
    },
    myPagination: {
      page: 1,
      total: 5,
      per_page: 0,
    },
    payload: {
      remarks: "",
      statusAction: 0,
      isCurrentStatus: 0,
      companyId: 0,
      userAdminId: 0,
    },

    rules: {
      remarks: "",
    },
  }),

  created() {},
  mounted() {
    this.fetchEmployees();
  },
  methods: {
    JobPosted(item) {
      this.$router.push({
        name: "JobListSelected",
        params: {
          id: item.uuid1,
        },
      });
    },

    ChangeStatus(item) {
      //for updating
      this.idload.id = item.id;
      this.idload.desc = item.businessName;

      //for adding log
      this.payload.statusAction = 4;
      this.payload.isCurrentStatus = 5;
      this.payload.companyId = item.id;
      this.payload.userAdminId = localStorage.getItem("id");

      this.Modal = true;
    },

    AddNotif() {
      let itemsString = JSON.stringify(this.items);
      itemsString = itemsString.slice(1, -1);
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "Notification/addNotif",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          notificationType: 6,
          recipientType: 2,
          senderId: localStorage.getItem("id"),
          recipientId: this.items[0].id,
          value: itemsString,
        },
      })
        .then((resp) => {})
        .catch((err) => {});
    },

    UpdateStatus() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "Employers/updateStatus",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.companyId,
          statusvalue: this.payload.statusAction,
        },
      })
        .then((resp) => {
          this.AddNotif();
          this.fetchEmployers();
          this.$refs.MySnackBar.showSuccessMessage("Save successfully!");
        })
        .catch((err) => {
          console.error(err);
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
      this.Modal = false;
      this.btnLoading = false;
    },

    addLog() {
      this.btnLoading = true;

      axios({
        method: "post",
        url: process.env.VUE_APP_API + "Employers/addLog",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: this.payload,
      })
        .then((resp) => {
          this.UpdateStatus();

          // this.btnLoading = false;
          //this.$refs.MySnackBar.showSuccessMessage('Congratulations! You have successfully suspended the employer!');
        })
        .catch((err) => {
          console.error(err);

          let error_status = err.response.status;

          if (error_status == 422) {
            this.rules.remarks = "The reason field is required.";
            this.btnLoading = false;
          } else {
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
            this.btnLoading = false;
          }
        });
    },

    searchItems() {
      this.fetchEmployees("search");
    },

    handleSearchInput() {
      clearTimeout(this.searchTimeout);

      this.searchTimeout = setTimeout(() => {
        this.searchItems();
      }, 500);
      this.myPagination.page = 1;
    },

    ViewProfile(item) {
      const route = this.$router.resolve({
        name: "EmpProfile",
        params: {
          id: item.uuid1,
        },
        query: {
          hidethis: item.approval_status == "Approved" ? "false" : "true",
        },
      });
      const routeUrl = route.href;
      window.open(routeUrl, "_blank");
    },

    fetchEmployees(paramType = null) {
      let myParameter = {
        page: 1,
        searchkey: "",
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCEmployees/fetchEmployees",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("xxx"),
          },
          params: myParameter,
        })
          .then((resp) => {
            this.items = resp.data.data;
            this.myPagination.total = resp.data.last_page;
            this.myPagination.per_page = resp.data.per_page;
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
          page: this.myPagination.page,
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
  overflow-x: auto;
}

.custom-title {
  background-color: #1770d6;
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

thead th {
  vertical-align: middle;
  background-color: #303847;
  color: white;
}
</style>
