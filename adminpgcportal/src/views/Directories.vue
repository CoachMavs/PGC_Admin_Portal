<template>
  <h1 class="card-header pb-3">Phone Directory</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>

    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <v-row>
          <v-col cols="12" md="10">
            <v-text-field
              v-model="searchkey"
              append-inner-icon="mdi-magnify"
              label="Search"
              clearable
              @update:model-value="handleSearchInput"
            />
          </v-col>
          <v-col cols="12" md="1">
            <v-btn
              color="#14727a"
              @click="printTelephoneDirectory"
              style="height: 55px; width: 100%"
              block
            >
              <div class="d-flex flex-column align-start">
                <span>Print</span>
              </div>
              <v-icon class="ml-2">mdi-printer-outline</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="12" md="1">
            <v-btn
              color="#14727a"
              @click="dialog = true"
              style="height: 55px; width: 100%"
              block
            >
              <div class="d-flex flex-column align-start">
                <span>Add</span>
              </div>
              <v-icon class="ml-2">mdi-newspaper-plus</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Office</th>
              <th scope="col">Tel No.</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.Nname }}</td>
              <td>{{ item.Office }}</td>
              <td>{{ item.TelNo }}</td>
              <td>
                <v-tooltip text="Edit" location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      class="mr-1 mb-1"
                      icon="mdi-file-edit"
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
                      @click="OpenDeleteDialog(item)"
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

  <!-- Modal Delete -->
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

  <!-- Modal Add/Edit -->
  <v-dialog v-model="dialog" persistent width="700">
    <v-card>
      <v-card-title class="custom-title"> Directory Details </v-card-title>

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
              <v-col cols="12" md="12">
                <v-text-field
                  v-model="payload.Nname"
                  label="Name"
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
              <v-col cols="10">
                <v-textarea
                  v-model="payload.Office"
                  label="Office"
                  color="#14727a"
                  variant="outlined"
                  rows="1"
                  auto-grow
                  hide-details="auto"
                  required
                  :rules="[required]"
                  validate-on="blur"
                />
              </v-col>

              <v-col cols="2">
                <v-text-field
                  v-model="payload.TelNo"
                  label="Tel No."
                  color="#14727a"
                  variant="outlined"
                  hide-details="auto"
                  required
                  :rules="[required]"
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

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */

import axios from "axios";
import "bootstrap";
import MySnackBar from "@/components/MySnackBar.vue";
import { mapActions } from "vuex";

export default {
  name: "FooTer",
  components: {
    MySnackBar,
  },
  data: () => ({
    Modal: false,
    dialog: false,

    searchkey: "",
    selectedItem: null,
    items: [],
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
      id: 0,
      Nname: "",
      Office: "",
      TelNo: "",
    },

    reportUri:
      process.env.VUE_APP_REPORT_BASE +
      "?" +
      encodeURI(
        "lo=" +
          localStorage
            .getItem("xxx")
            .substring(
              localStorage.getItem("xxx").indexOf("|") + 1,
              localStorage.getItem("xxx").length
            ) +
          "&p=telephonedirectory"
      ),
  }),

  created() {},
  mounted() {
    this.fetch();
  },
  setup() {
    function required(v) {
      return !!v || "Field is required";
    }
    return { required };
  },
  methods: {
    printTelephoneDirectory() {
      window.location.href = this.reportUri;
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

    fetch(paramType = null) {
      let myParameter = {
        page: 1,
        searchkey: "",
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "PGCDirectories/fetch",
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

    update() {
      if (!this.$refs.form.validate()) {
        return;
      }
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCDirectories/updateDirectory",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          ...this.payload,
        },
      })
        .then((resp) => {
          this.fetch();
          this.$refs.MySnackBar.showSuccessMessage("Records successfully updated!");
          this.clearInputs();
        })
        .catch((err) => {
          console.error(err);
          if (err.response.status === 422) {
            this.$refs.MySnackBar.showErrorMessage("Please fill up required fields");
          } else if (
            err.response.status === 409 || // HTTP 409 Conflict
            (err.response.data &&
              err.response.data.message &&
              err.response.data.message.includes("duplicate"))
          ) {
            this.$refs.MySnackBar.showErrorMessage(
              "Tel No. already exists. Please use a unique Tel No."
            );
          } else {
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
          }
        });
      this.btnLoading = false;
    },

    clearInputs() {
      this.payload.id = 0;
      this.payload.Nname = "";
      this.payload.Office = "";
      this.payload.TelNo = "";
      this.dialog = false;
    },

    DeleteReq() {
      this.btnLoading = true;
      axios({
        method: "post",
        url: process.env.VUE_APP_API + "PGCDirectories/DeleteReq",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
        data: {
          id: this.payload.id,
        },
      })
        .then((resp) => {
          this.fetch();
          this.$refs.MySnackBar.showSuccessMessage("Record succesfully deleted!");
        })
        .catch((err) => {
          console.error(err);
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
      this.Modal = false;
      this.btnLoading = false;
    },

    OpenDialog(item) {
      this.payload.id = item.ID;
      this.payload.Nname = item.Nname;
      this.payload.Office = item.Office;
      this.payload.TelNo = item.TelNo;
      this.dialog = true;
    },
    OpenDeleteDialog(item) {
      this.payload.id = item.ID;
      this.Modal = true;
    },
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}

.custom-title {
  /* background-color: #1770d6;
  color: white; */
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
