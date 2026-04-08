/* eslint-disable */
<template>
  <v-app>
    <v-main>
      <v-container fluid>
        <v-row align="center" justify="center">
          <v-col v-for="card in dashboardCards" :key="card.key" cols="12" sm="6" md="4">
            <v-card
              class="dashboard-card"
              :style="card.style"
              @click="handleSidebarClick(card.sidebarId)"
            >
              <div class="text-h3 font-weight-bold">
                {{ dashboardData[card.key] }}
              </div>
              <div class="mt-1">
                <v-icon size="24" class="mr-1">{{ card.icon }}</v-icon>
                <span class="text-subtitle-1">{{ card.label }}</span>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
/* eslint-disable */
import axios from "axios";
import echo from "./echo";
import { ref, onMounted, onBeforeUnmount, inject } from "vue";

export default {
  name: "Dashboard",
  setup() {
    const dashboardData = inject("dashboardData");
    return { dashboardData };
  },

  data: () => ({
    playthis: "",
    dashboardCards: [
      {
        key: "postInspectionRequests",
        label: "Post Inspection Requests",
        icon: "mdi-video-outline",
        style: "background-color: #0a192f; color: #2196f3",
        sidebarId: "#sidebar-post",
      },
      {
        key: "wasteCertificationRequests",
        label: "Waste Certification Requests",
        icon: "mdi-magnify",
        style: "background-color: #003344; color: #00bcd4",
        sidebarId: "#sidebar-waste",
      },
      {
        key: "newZoomRequests",
        label: "New Zoom Requests",
        icon: "mdi-calendar-check-outline",
        style: "background-color: #1a1f40; color: #7986cb",
        sidebarId: "#sidebar-zoom-request",
      },
      {
        key: "upcomingZoomMeetings",
        label: "Upcoming Zoom Meetings",
        icon: "mdi-calendar-edit",
        style: "background-color: #2c0f00; color: #ff9800",
        sidebarId: "#sidebar-zoom-upcoming",
      },
      {
        key: "repairRequestsForApproval",
        label: "Repair Requests (For Approval)",
        icon: "mdi-download-outline",
        style: "background-color: #1f1030; color: #ba68c8",
        sidebarId: "#sidebar-repairs-request",
      },
      {
        key: "repairRequestsForReceiving",
        label: "Repair Requests (For Receiving)",
        icon: "mdi-check-circle-outline",
        style: "background-color: #002d1a; color: #00e676",
        sidebarId: "#sidebar-repairs-for-receiving",
      },
      {
        key: "ongoingRepair",
        label: "On-going Repair",
        icon: "mdi-progress-wrench",
        style: "background-color: #2b2000; color: #ffc107",
        sidebarId: "#sidebar-current-repairs",
      },
      {
        key: "forPickUpRepaired",
        label: "For Pick-up Repaired",
        icon: "mdi-clock-outline",
        style: "background-color: #003d33; color: #00e676",
        sidebarId: "#sidebar-current-repairs",
      },
      {
        key: "forPickupNotRepaired",
        label: "For Pickup Not Repaired",
        icon: "mdi-wrench-outline",
        style: "background-color: #330000; color: #ef5350",
        sidebarId: "#sidebar-current-repairs",
      },
    ],

    // dashboardData: {
    //   postInspectionRequests: 0,
    //   wasteCertificationRequests: 0,
    //   newZoomRequests: 0,
    //   upcomingZoomMeetings: 0,
    //   repairRequestsForApproval: 0,
    //   repairRequestsForReceiving: 0,
    //   ongoingRepair: 0,
    //   forPickUpRepaired: 0,
    //   forPickupNotRepaired: 0,
    // },

    fetchLoading: false,
    controller: null,
  }),

  // mounted() {
  //   this.fetchDashboardData();

  //   this.channel = echo.channel("chat").listen(".message.sent", (e) => {
  //     switch (e.message) {
  //       case "triggerPostInspection":
  //         this.fetchPostInspection();
  //         break;
  //       case "triggerWasteCertificate":
  //         this.fetchWasteCerticate();
  //         break;
  //       case "triggerZoomPending":
  //         this.fetchNewZoomRequest();
  //         break;
  //       case "triggerZoomUpcoming":
  //         this.fetchUpcomingZoom();
  //         break;
  //       case "triggerPendingRepairs":
  //         this.fetchForApproval();
  //         break;
  //       case "triggerForReceivingRepairs":
  //         this.fetchForReceiving();
  //         break;
  //       case "triggerCurrentRepairs":
  //         this.fetchOngoing();
  //         this.fetchRepaired();
  //         this.fetchNotRepaired();
  //         break;
  //       default:
  //         console.warn("Unhandled message:", e.message);
  //     }
  //   });
  // },

  // beforeUnmount() {
  //   if (this.channel) {
  //     this.channel.stopListening(".message.sent");
  //   }
  // },

  methods: {
    // playSound(filePath) {
    //   const audio = new Audio(filePath);
    //   audio.play();
    // },

    // delay(ms) {
    //   return new Promise((resolve) => setTimeout(resolve, ms));
    // },
    handleSidebarClick(sidebarId) {
      const sidebarItem = document.querySelector(sidebarId);
      if (sidebarItem) {
        sidebarItem.click();
      } else {
        console.warn(`Sidebar element not found: ${sidebarId}`);
      }
    },
    // async fetchDashboardData() {
    //   if (this.controller) this.controller.abort();
    //   this.controller = new AbortController();
    //   this.fetchLoading = true;

    //   const headers = {
    //     Authorization: "Bearer " + localStorage.getItem("xxx"),
    //   };
    //   const config = {
    //     headers,
    //     signal: this.controller.signal,
    //   };

    //   try {
    //     const baseURL = process.env.VUE_APP_API + "Dash/";

    //     const postInspection = await axios.get(baseURL + "fetchPostInspection", config);
    //     await this.delay(200);

    //     const wasteCert = await axios.get(baseURL + "fetchWasteCerticate", config);
    //     await this.delay(200);

    //     const newZoom = await axios.get(baseURL + "fetchNewZoomRequest", config);
    //     await this.delay(200);

    //     const upcomingZoom = await axios.get(baseURL + "fetchUpcomingZoom", config);
    //     await this.delay(200);

    //     const forApproval = await axios.get(baseURL + "fetchForApproval", config);
    //     await this.delay(200);

    //     const forReceiving = await axios.get(baseURL + "fetchForReceiving", config);
    //     await this.delay(200);

    //     const ongoing = await axios.get(baseURL + "fetchOngoing", config);
    //     await this.delay(200);

    //     const repaired = await axios.get(baseURL + "fetchRepaired", config);
    //     await this.delay(200);

    //     const notRepaired = await axios.get(baseURL + "fetchNotRepaired", config);

    //     this.dashboardData.postInspectionRequests = postInspection.data ?? 0;
    //     this.dashboardData.wasteCertificationRequests = wasteCert.data ?? 0;
    //     this.dashboardData.newZoomRequests = newZoom.data ?? 0;
    //     this.dashboardData.upcomingZoomMeetings = upcomingZoom.data ?? 0;
    //     this.dashboardData.repairRequestsForApproval = forApproval.data ?? 0;
    //     this.dashboardData.repairRequestsForReceiving = forReceiving.data ?? 0;
    //     this.dashboardData.ongoingRepair = ongoing.data ?? 0;
    //     this.dashboardData.forPickUpRepaired = repaired.data ?? 0;
    //     this.dashboardData.forPickupNotRepaired = notRepaired.data ?? 0;
    //   } catch (err) {
    //     if (err.name === "CanceledError" || err.code === "ERR_CANCELED") {
    //       console.log("API call was canceled.");
    //     } else {
    //       console.error("Error during dashboard fetch:", err);
    //     }
    //   } finally {
    //     this.fetchLoading = false;
    //   }
    // },

    // // Keep all individual fetch methods for future real-time use
    // fetchPostInspection() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchPostInspection",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       if (this.dashboardData.postInspectionRequests < resp.data) {
    //         this.playSound("/mp3/post.mp3");
    //       }
    //       this.dashboardData.postInspectionRequests = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },

    // fetchWasteCerticate() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchWasteCerticate",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       if (this.dashboardData.wasteCertificationRequests < resp.data) {
    //         this.playSound("/mp3/waste.mp3");
    //       }
    //       this.dashboardData.wasteCertificationRequests = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },
    // fetchNewZoomRequest() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchNewZoomRequest",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       if (this.dashboardData.newZoomRequests < resp.data) {
    //         this.playSound("/mp3/zoom.mp3");
    //       }
    //       this.dashboardData.newZoomRequests = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },
    // fetchUpcomingZoom() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchUpcomingZoom",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       this.dashboardData.upcomingZoomMeetings = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },
    // fetchForApproval() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchForApproval",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       if (this.dashboardData.repairRequestsForApproval < resp.data) {
    //         this.playSound("/mp3/repairs.mp3");
    //       }

    //       this.dashboardData.repairRequestsForApproval = resp.data;

    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },

    // fetchForReceiving() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchForReceiving",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       this.dashboardData.repairRequestsForReceiving = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },

    // fetchOngoing() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchOngoing",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       this.dashboardData.ongoingRepair = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },
    // fetchRepaired() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchRepaired",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       this.dashboardData.forPickUpRepaired = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },
    // fetchNotRepaired() {
    //   this.fetchLoading = true;
    //   axios({
    //     method: "get",
    //     url: process.env.VUE_APP_API + "Dash/fetchNotRepaired",
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("xxx"),
    //     },
    //   })
    //     .then((resp) => {
    //       this.dashboardData.forPickupNotRepaired = resp.data;
    //       this.fetchLoading = false;
    //     })
    //     .catch((err) => {
    //       this.fetchLoading = false;
    //       console.log(err.errorMessages);
    //     });
    // },
  },
};
</script>

<style scoped>
html,
body,
.v-application {
  height: 100%;
  margin: 0;
}
.fill-height {
  min-height: 100vh;
}
.dashboard-card {
  padding: 1.5rem;
  text-align: center;
  border-radius: 1rem;
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  cursor: pointer;
  height: 200px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.dashboard-card:hover {
  transform: scale(1.05);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
}
</style>
