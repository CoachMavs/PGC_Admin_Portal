<template>
  <div class="wrapper">
    <SideNavBar ref="SideNavBar" />
    <div class="main">
      <AppBar ref="AppBar" />
      <main class="content">
        <div class="container-fluid p-0">
          <router-view />
        </div>
      </main>
      <FooTer ref="FooTer" />
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import SideNavBar from "../components/SideNavBar.vue";
import AppBar from "../components/AppBar.vue";
import FooTer from "../components/FooTer.vue";
import echo from "../views/echo";
import "bootstrap";
import axios from "axios";
import { ref, onMounted, onBeforeUnmount, reactive, provide } from "vue";

export default {
  name: "LayoutAuthenticated",
  components: {
    SideNavBar,
    AppBar,
    FooTer,
  },
  setup() {
    const dashboardData = reactive({
      postInspectionRequests: 0,
      wasteCertificationRequests: 0,
      newZoomRequests: 0,
      upcomingZoomMeetings: 0,
      repairRequestsForApproval: 0,
      repairRequestsForReceiving: 0,
      ongoingRepair: 0,
      forPickUpRepaired: 0,
      forPickupNotRepaired: 0,
    });
    provide("dashboardData", dashboardData);

    return { dashboardData };
  },

  data: () => ({
    playthis: "",
    intervalId: null,
    controller: null,
    items: [],
  }),

  mounted() {
    this.fetchDashboardData();

    this.channel = echo.channel("chat").listen(".message.sent", (e) => {
      switch (e.message) {
        case "triggerPostInspection":
          this.fetchPostInspection();
          e.message = "";
          break;
        case "triggerWasteCertificate":
          this.fetchWasteCerticate();
          break;
        case "triggerZoomPending":
          this.fetchNewZoomRequest();
          break;
        case "triggerZoomUpcoming":
          this.fetchUpcomingZoom();
          break;
        case "triggerPendingRepairs":
          this.fetchForApproval();
          break;
        case "triggerForReceivingRepairs":
          this.fetchForReceiving();
          break;
        case "triggerCurrentRepairs":
          this.fetchOngoing();
          this.fetchRepaired();
          this.fetchNotRepaired();
          break;
        default:
          console.warn("Unhandled message:", e.message);
      }
    });
  },

  beforeUnmount() {
    if (this.channel) {
      this.channel.stopListening(".message.sent");
    }
  },

  methods: {
    playSound(filePath) {
      const audio = new Audio(filePath);
      audio.play();
    },

    delay(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },

    async fetchDashboardData() {
      if (this.controller) this.controller.abort();
      this.controller = new AbortController();
      this.fetchLoading = true;

      const headers = {
        Authorization: "Bearer " + localStorage.getItem("xxx"),
      };
      const config = {
        headers,

        signal: this.controller.signal,
      };

      try {
        const baseURL = process.env.VUE_APP_API + "Dash/";

        const postInspection = await axios.get(baseURL + "fetchPostInspection", config);
        await this.delay(200);

        const wasteCert = await axios.get(baseURL + "fetchWasteCerticate", config);
        await this.delay(200);

        const newZoom = await axios.get(baseURL + "fetchNewZoomRequest", config);
        await this.delay(200);

        const upcomingZoom = await axios.get(baseURL + "fetchUpcomingZoom", config);
        await this.delay(200);

        const forApproval = await axios.get(baseURL + "fetchForApproval", config);
        await this.delay(200);

        const forReceiving = await axios.get(baseURL + "fetchForReceiving", config);
        await this.delay(200);

        const ongoing = await axios.get(baseURL + "fetchOngoing", config);
        await this.delay(200);

        const repaired = await axios.get(baseURL + "fetchRepaired", config);
        await this.delay(200);

        const notRepaired = await axios.get(baseURL + "fetchNotRepaired", config);

        this.dashboardData.postInspectionRequests = postInspection.data ?? 0;
        this.dashboardData.wasteCertificationRequests = wasteCert.data ?? 0;
        this.dashboardData.newZoomRequests = newZoom.data ?? 0;
        this.dashboardData.upcomingZoomMeetings = upcomingZoom.data ?? 0;
        this.dashboardData.repairRequestsForApproval = forApproval.data ?? 0;
        this.dashboardData.repairRequestsForReceiving = forReceiving.data ?? 0;
        this.dashboardData.ongoingRepair = ongoing.data ?? 0;
        this.dashboardData.forPickUpRepaired = repaired.data ?? 0;
        this.dashboardData.forPickupNotRepaired = notRepaired.data ?? 0;
      } catch (err) {
        if (err.name === "CanceledError" || err.code === "ERR_CANCELED") {
          console.log("API call was canceled.");
        } else {
          console.error("Error during dashboard fetch:", err);
        }
      } finally {
        this.fetchLoading = false;
      }
    },

    // Keep all individual fetch methods for future real-time use
    fetchPostInspection() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchPostInspection",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          if (this.dashboardData.postInspectionRequests < resp.data) {
            this.playSound("/mp3/post.mp3");
          }
          this.dashboardData.postInspectionRequests = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },

    fetchWasteCerticate() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchWasteCerticate",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          if (this.dashboardData.wasteCertificationRequests < resp.data) {
            this.playSound("/mp3/waste.mp3");
          }
          this.dashboardData.wasteCertificationRequests = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },
    fetchNewZoomRequest() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchNewZoomRequest",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          if (this.dashboardData.newZoomRequests < resp.data) {
            this.playSound("/mp3/zoom.mp3");
          }
          this.dashboardData.newZoomRequests = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },
    fetchUpcomingZoom() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchUpcomingZoom",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.dashboardData.upcomingZoomMeetings = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },
    fetchForApproval() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchForApproval",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          if (this.dashboardData.repairRequestsForApproval < resp.data) {
            this.playSound("/mp3/repairs.mp3");
          }

          this.dashboardData.repairRequestsForApproval = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },

    fetchForReceiving() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchForReceiving",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.dashboardData.repairRequestsForReceiving = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },

    fetchOngoing() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchOngoing",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.dashboardData.ongoingRepair = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },
    fetchRepaired() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchRepaired",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.dashboardData.forPickUpRepaired = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },
    fetchNotRepaired() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dash/fetchNotRepaired",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.dashboardData.forPickupNotRepaired = resp.data;
          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          console.log(err.errorMessages);
        });
    },
  },
};
</script>

<style scoped>
html,
body {
  overflow: visible;
  height: auto;
}
.main {
  overflow: visible;
}
</style>

<style lang="css" src="../assets/css/app.css"></style>
