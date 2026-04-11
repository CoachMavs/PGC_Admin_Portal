<template>
  <router-view>
    <nav id="sidebar" class="sidebar js-sidebar">
      <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand">
          <div class="brand-container">
            <img
              v-if="items.length === 0"
              :src="require('../assets/img/ISU_LOGO.png')"
              class="avatar img-fluid rounded me-1"
              alt="Charles Hall"
            />

            <!-- ✅ Otherwise loop through items -->
            <img
              v-else
              v-for="(item, index) in items"
              :key="index"
              :src="item.src || require('../assets/img/ISU_LOGO.png')"
              class="avatar img-fluid rounded me-1"
              alt="Charles Hall"
            />

            <span class="align-middle">PGC PORTAL ADMIN</span>
          </div>
        </a>

        <ul class="sidebar-nav">
          <!-- Dashboard -->
          <li class="sidebar-item" :class="{ active: isActive('Dashboard') }">
            <a class="sidebar-link">
              <i :class="getIconClass('Dashboard')" class="align-middle icon-size"></i>
              <span class="align-middle" @click="goToDashboard()">Dashboard</span>
            </a>
          </li>

          <!-- Employees -->
          <li class="sidebar-item" :class="{ active: isActive('Employees') }">
            <a class="sidebar-link" @click="goToEmployees">
              <i :class="getIconClass('Employees')" class="align-middle icon-size"></i>
              <span class="align-middle">Employees</span>
            </a>
          </li>

          <!-- Phone Directory -->
          <li class="sidebar-item" :class="{ active: isActive('Directories') }">
            <a class="sidebar-link" @click="goToDirectories">
              <i
                :class="getIconClass('Phone Directory')"
                class="align-middle icon-size"
              ></i>
              <span class="align-middle">Phone Directory</span>
            </a>
          </li>

          <!-- 1 toggle -->
          <li class="sidebar-item">
            <button
              class="sidebar-link w-100 d-flex justify-content-between align-items-center"
              @click="toggle1"
              :class="{ active: !Collapsed1 }"
            >
              <span
                class="align-middle"
                :style="{ color: buttonClicked1 ? 'white' : '#899499' }"
              >
                <i
                  :class="getIconClass('Zoom Meeting')"
                  class="align-middle icon-size"
                ></i>
                Zoom Meetings
              </span>
              <i :class="Collapsed1 ? 'bi bi-caret-left' : 'bi bi-caret-down'"></i>
            </button>

            <div :class="['collapse', { show: !Collapsed1 }]">
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('ZoomRequest') }"
              >
                <a class="sidebar-link" @click="goToZoomRequest">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('New Requests')"
                      class="align-middle icon-size"
                      id="sidebar-zoom-request"
                    ></i>
                    <span class="align-middle">New Requests</span>
                  </div>
                </a>
              </li>
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('ZoomUpcoming') }"
              >
                <a class="sidebar-link" @click="goToZoomUpcoming">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Upcoming Meetings')"
                      class="align-middle icon-size"
                      id="sidebar-zoom-upcoming"
                    ></i>
                    <span class="align-middle">Upcoming Meetings</span>
                  </div>
                </a>
              </li>
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('ZoomPrevious') }"
              >
                <a class="sidebar-link" @click="goToZoomPrevious">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Previous Meeting')"
                      class="align-middle icon-size"
                    ></i>
                    <span class="align-middle">Previous Meeting</span>
                  </div>
                </a>
              </li>
            </div>
          </li>

          <!-- 2 with Toggle Mode -->
          <li class="sidebar-item">
            <button
              class="sidebar-link w-100 d-flex justify-content-between align-items-center"
              @click="toggle2"
              :class="{ active: !Collapsed2 }"
            >
              <span
                class="align-middle"
                :style="{ color: buttonClicked2 ? 'white' : '#899499' }"
              >
                <i :class="getIconClass('Repairs')" class="align-middle icon-size"></i>
                Repairs
              </span>
              <i :class="Collapsed2 ? 'bi bi-caret-left' : 'bi bi-caret-down'"></i>
            </button>

            <div :class="['collapse', { show: !Collapsed2 }]">
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('RepairsRequest') }"
              >
                <a class="sidebar-link" @click="goToRepairRequest">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('New Request')"
                      class="align-middle icon-size"
                      id="sidebar-repairs-request"
                    ></i>
                    <span class="align-middle">For Approval</span>
                  </div>
                </a>
              </li>
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('RepairsForReceiving') }"
              >
                <a class="sidebar-link" @click="goToRepairReceiving">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('For Receiving')"
                      class="align-middle icon-size"
                      id="sidebar-repairs-for-receiving"
                    ></i>
                    <span class="align-middle">For Receiving</span>
                  </div>
                </a>
              </li>
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('RepairsCurrent') }"
              >
                <a
                  class="sidebar-link"
                  @click="goToRepairCurrent"
                  id="sidebar-current-repairs"
                >
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Current Repairs')"
                      class="align-middle icon-size"
                    ></i>
                    <span class="align-middle">Current Repairs</span>
                  </div>
                </a>
              </li>

              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('RepairsPrevious') }"
              >
                <a class="sidebar-link" @click="goToRepairPrevious">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Previous Repairs')"
                      class="align-middle icon-size"
                    ></i>
                    <span class="align-middle">Previous Repairs</span>
                  </div>
                </a>
              </li>
            </div>
          </li>

          <!-- 3 with Toggle Mode -->

          <li class="sidebar-item">
            <button
              class="sidebar-link w-100 d-flex justify-content-between align-items-center"
              @click="toggle3"
              :class="{ active: !Collapsed3 }"
            >
              <span
                class="align-middle"
                :style="{ color: buttonClicked3 ? 'white' : '#899499' }"
              >
                <i
                  :class="getIconClass('Certifications')"
                  class="align-middle icon-size"
                ></i>
                Certifications
              </span>
              <i :class="Collapsed3 ? 'bi bi-caret-left' : 'bi bi-caret-down'"></i>
            </button>

            <div :class="['collapse', { show: !Collapsed3 }]">
              <li class="sidebar-item sub-item" :class="{ active: isActive('CertPre') }">
                <a class="sidebar-link" @click="goToCertPre">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Pre-Inspection')"
                      class="align-middle icon-size"
                    ></i>
                    <span class="align-middle">Pre-Inspection</span>
                  </div>
                </a>
              </li>
              <li class="sidebar-item sub-item" :class="{ active: isActive('CertPost') }">
                <a class="sidebar-link" @click="goToCertPost">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Post-Inspection')"
                      class="align-middle icon-size"
                      id="sidebar-post"
                    ></i>
                    <span class="align-middle">Post-Inspection</span>
                  </div>
                </a>
              </li>
              <li
                class="sidebar-item sub-item"
                :class="{ active: isActive('CertWaste') }"
              >
                <a class="sidebar-link" @click="goToCertWaste">
                  <div class="pl-4">
                    <i
                      :class="getIconClass('Waste')"
                      class="align-middle icon-size"
                      id="sidebar-waste"
                    ></i>
                    <span class="align-middle">Waste Certifications</span>
                  </div>
                </a>
              </li>
            </div>
          </li>

          <!-- Reports Section -->
          <li class="sidebar-header">Export to Excel</li>

          <li class="sidebar-item" :class="{ active: isActive('ExportToExcelRepairs') }">
            <a class="sidebar-link" @click="goToExport">
              <i :class="getIconClass('Report1')" class="align-middle icon-size"></i>
              <span class="align-middle">Repair logs</span>
            </a>
          </li>

          <li class="sidebar-item" :class="{ active: isActive('ExportToExcelZoom') }">
            <a class="sidebar-link" @click="goToExportZoom">
              <i :class="getIconClass('Report2')" class="align-middle icon-size"></i>
              <span class="align-middle">Zoom Meetings</span>
            </a>
          </li>

          <!-- link to isu system -->
          <!-- <li class="sidebar-header">Linked Site</li>
          <li class="sidebar-item">
            <a class="sidebar-link" @click.prevent="goToMono1()">
              <i :class="getIconClass('Linked')" class="align-middle icon-size"></i>
              <span class="align-middle">ISU Reports</span>
            </a>
          </li>

          <li class="sidebar-header">iFrame</li>
          <li class="sidebar-item">
            <a class="sidebar-link" @click.prevent="goToiFrame()">
              <i :class="getIconClass('Linked')" class="align-middle icon-size"></i>
              <span class="align-middle">ISU Reports</span>
            </a>
          </li> -->

          <!-- Accounts Section -->
          <li class="sidebar-header">Accounts</li>

          <li class="sidebar-item" :class="{ active: isActive('Logout') }">
            <a class="sidebar-link" @click="logout">
              <i :class="getIconClass('Logout')" class="align-middle icon-size"></i>
              <span class="align-middle">Logout</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Add this below your nav -->
    <iframe
      v-if="showIframe"
      :src="iframeSrc"
      style="
        position: fixed;
        top: 0;
        left: 0;
        margin-left: 250px; /* Adjust to your sidebar width */
        width: calc(100% - 250px); /* Adjust to your sidebar width */
        height: 80vh;
        border: none;
        z-index: 9999;
        background: white;
      "
    ></iframe>
    <!-- Optionally add a close button -->
    <button
      v-if="showIframe"
      @click="showIframe = false"
      style="position: fixed; top: 10px; right: 10px; z-index: 10000"
    >
      Close
    </button>
  </router-view>
</template>

<script>
/* eslint-disable */
import axios from "axios";
import { useRoute } from "vue-router";
import feather from "feather-icons";
import SimpleBar from "simplebar";
import "bootstrap-icons/font/bootstrap-icons.css";
import { ref } from "vue";

export default {
  name: "App",
  data() {
    return {
      Collapsed1: true,
      Collapsed2: true,
      Collapsed3: true,
      buttonClicked1: false,
      buttonClicked2: false,
      buttonClicked3: false,
      items: [],
      showIframe: false, // Add this
      iframeSrc: "",
    };
  },
  setup() {
    const route = useRoute();
    const isActive = (routeName) => route.name === routeName;
    return { isActive };
  },
  mounted() {
    this.fetchPix();
    this.initializeSimplebar();
    try {
      feather.replace();
    } catch (e) {
      console.error("Error initializing feather icons:", e);
    }
  },
  methods: {
    fetchPix() {
      this.fetchLoading = true;
      axios({
        method: "get",
        url: process.env.VUE_APP_API + "Dashboard/fetchPhotos",
        headers: {
          Authorization: "Bearer " + localStorage.getItem("xxx"),
        },
      })
        .then((resp) => {
          this.items = resp.data.files || [];

          this.fetchLoading = false;
        })
        .catch((err) => {
          this.fetchLoading = false;
          this.$refs.MySnackBar.showErrorMessage("Something went wrong!");
        });
    },

    goToMono() {
      this.iframeSrc = "https://www.youtube.com/embed/dQw4w9WgXcQ";
      this.showIframe = true;
    },

    goToMono1() {
      const token = localStorage.getItem("xxx");

      if (!token) {
        alert("Missing token");
        return;
      }

      // const redirectUrl = `http://localhost:8001/auth/redirect?token=${token}`;
      // const redirectUrl = `http://172.16.50.54:8080/auth/redirect?token=${token}`;

      // const redirectUrl = `${process.env.VUE_APP_LINK_BASE}auth/redirect?token=${token}`;

      const redirectUrl = `https://tasks.cagayan.gov.ph/auth/redirect?token=${token}`;

      window.open(redirectUrl, "_blank");
    },

    initializeSidebarCollapse() {
      const sidebarElement = document.getElementsByClassName("js-sidebar")[0];
      const sidebarToggleElement = document.getElementsByClassName(
        "js-sidebar-toggle"
      )[0];

      if (sidebarElement && sidebarToggleElement) {
        sidebarElement.classList.toggle("collapsed");

        sidebarElement.addEventListener("transitionend", () => {
          window.dispatchEvent(new Event("resize"));
        });
      }
    },
    toggleFullscreen() {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
      } else if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    },

    toggle1() {
      this.Collapsed1 = !this.Collapsed1;
      this.buttonClicked1 = !this.buttonClicked1;
      this.$nextTick(() => {
        this.recalculateSimpleBar();
      });
    },
    toggle2() {
      this.Collapsed2 = !this.Collapsed2;
      this.buttonClicked2 = !this.buttonClicked2;
      this.$nextTick(() => {
        this.recalculateSimpleBar();
      });
    },
    toggle3() {
      this.Collapsed3 = !this.Collapsed3;
      this.buttonClicked3 = !this.buttonClicked3;
    },

    async logout() {
      axios
        .post(
          `${process.env.VUE_APP_API}auth/logout`,
          {},
          {
            headers: { Authorization: `Bearer ${localStorage.getItem("xxx")}` },
          }
        )
        .finally(() => {
          localStorage.removeItem("xxx");
          localStorage.removeItem("id");
          localStorage.removeItem("DN");
          this.$router.push({ name: "Login" }).then(() => {
            document.body.style.backgroundColor = "white";
            window.location.reload();
          });
        });
    },
    // async logout() {
    //   const token = localStorage.getItem("xxx");
    //   const headers = {
    //     Authorization: `Bearer ${token}`,
    //   };

    //   try {
    //     await axios.post(`${process.env.VUE_APP_API}auth/logout`, {}, { headers });

    //     // window.location.href = `http://localhost:8001/logout?token=${token}&redirect=http://localhost:8080/`;
    //     window.location.href = `https://tasks.cagayan.gov.ph/logout?token=${token}&redirect=https://admin.portal.cagayan.gov.ph/`;
    //     return;
    //   } catch (error) {
    //     console.error("Logout error:", error);

    //     localStorage.removeItem("xxx");
    //     localStorage.removeItem("id");
    //     localStorage.removeItem("DN");
    //     this.$router.push({ name: "Login" });
    //   }
    // },

    goToDashboard() {
      this.$router.push({ name: "Dashboard" });
    },
    goToExport() {
      this.$router.push({ name: "ExportToExcelRepairs" });
    },
    goToExportZoom() {
      this.$router.push({ name: "ExportToExcelZoom" });
    },
    // goToDashboard() {
    //   this.$router.push({ name: "Dashboard" });
    // },
    goToEmployees() {
      this.$router.push({ name: "Employees" });
    },
    goToDirectories() {
      this.$router.push({ name: "Directories" });
    },
    goToZoomRequest() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "ZoomRequest" });
    },
    goToZoomUpcoming() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "ZoomUpcoming" });
    },
    goToZoomPrevious() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "ZoomPrevious" });
    },
    goToRepairRequest() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "RepairsRequest" });
    },
    goToRepairReceiving() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "RepairsForReceiving" });
    },
    goToRepairCurrent() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "RepairsCurrent" });
    },
    goToRepairPrevious() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "RepairsPrevious" });
    },
    goToCertPre() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "CertPre" });
    },

    goToCertPost() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "CertPost" });
    },
    goToCertWaste() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "CertWaste" });
    },
    goToDEmployers() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "DEmployers" });
    },
    goToiFrame() {
      this.initializeSidebarCollapse();
      this.$router.push({ name: "GoToMono" });
    },
    initializeSimplebar() {
      const simplebarElement = document.querySelector(".js-simplebar");
      if (simplebarElement) {
        const simplebarInstance = new SimpleBar(simplebarElement);
        const sidebarDropdowns = document.querySelectorAll(
          ".js-sidebar [data-bs-parent]"
        );
        sidebarDropdowns.forEach((link) => {
          link.addEventListener("shown.bs.collapse", () =>
            simplebarInstance.recalculate()
          );
          link.addEventListener("hidden.bs.collapse", () =>
            simplebarInstance.recalculate()
          );
        });
      }
    },
    getIconClass(text) {
      switch (text) {
        case "Dashboard":
          return "mdi mdi-view-dashboard";
        case "Employees":
          return "mdi mdi-account-group";
        case "Phone Directory":
          return "mdi mdi-phone";
        case "Zoom Meeting":
          return "mdi mdi-video";
        case "New Requests":
          return "mdi mdi-check-circle";
        case "Upcoming Meetings":
          return "mdi mdi-calendar";
        case "Previous Meeting":
          return "mdi mdi-history";
        case "Repair Log Sheet":
          return "mdi mdi-file-document";
        case "New Request":
          return "mdi mdi-check-circle";
        case "For Approval":
          return "mdi mdi-clipboard-check-outline";
        case "For Receiving":
          return "mdi mdi-truck-delivery-outline";
        case "Current Repairs":
          return "mdi mdi-wrench";
        case "Previous Repairs":
          return "mdi mdi-history";
        case "Pre-Inspection":
          return "mdi mdi-magnify";
        case "Post-Inspection":
          return "mdi mdi-clipboard-check";
        case "Waste":
          return "mdi mdi-recycle";
        case "Certifications":
          return "mdi mdi-certificate";
        case "Repairs":
          return "mdi mdi-tools";
        case "Linked":
          return "mdi mdi-link-variant";
        case "Report1":
          return "mdi mdi-tools";
        case "Report2":
          return "mdi mdi-video";
        case "Logout":
          return "mdi mdi-logout";
        default:
          return "mdi mdi-file";
      }
    },

    recalculateSimpleBar() {
      this.$nextTick(() => {
        const simplebarElement = document.querySelector(".js-simplebar");
        if (simplebarElement) {
          new SimpleBar(simplebarElement).recalculate();
        }
      });
    },
  },
};
</script>

<style scoped>
.brand-container {
  text-align: center;
  margin-bottom: 20px;
}

.brand-container span {
  display: block;
  margin-top: 10px;
  color: #fff;
}

.sidebar-link.active i {
  color: #ffc181;
  /* Optional: change icon color when active */
}
/* arrow color when expanded */
.sidebar .expandEmp i {
  color: #ffc181;
}

/* Handle long text */
.sidebar-link span {
  display: inline-block;
  max-width: 150px; /* Adjust the width as needed */
  white-space: nowrap;
  text-overflow: ellipsis;
}

.sidebar-link i {
  margin-right: 5px; /* Add space between icon and text */
}

.icon-size {
  font-size: 24px; /* Adjust the size as needed */
}
.sidebar-content {
  max-height: 100vh; /* Prevents overflow beyond viewport */
  overflow-y: auto; /* Enables scrolling when content overflows */
}
.brand-container img {
  width: 100%; /* Keep the image full size */
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
</style>
