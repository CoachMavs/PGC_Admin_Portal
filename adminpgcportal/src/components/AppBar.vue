<template>
  <nav
    class="navbar navbar-expand appbar-fixed"
    :class="[themeClass, 'bg-background', 'text-on-background']"
  >
    <div class="container-fluid">
      <!-- Sidebar Toggle -->
      <a class="sidebar-toggle js-sidebar-toggle" @click="initializeSidebarCollapse">
        <i class="hamburger align-self-center"></i>
      </a>

      <!-- Theme Toggle Button -->
      <!-- 
      <v-btn
        icon
        variant="text"
        class="mx-2"
        @click="toggleTheme"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
      >
        <v-icon>{{ isDark ? "mdi-weather-night" : "mdi-white-balance-sunny" }}</v-icon>
      </v-btn> 
      -->

      <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
          <!-- Fullscreen Icon -->
          <li class="nav-item">
            <a class="nav-link" href="#" @click="toggleFullscreen">
              <i class="align-middle" data-feather="maximize"></i>
            </a>
          </li>

          <!-- User Profile Dropdown -->
          <li class="nav-item dropdown">
            <!-- Mobile settings icon -->
            <a
              class="nav-icon dropdown-toggle d-inline-block d-sm-none"
              href="#"
              data-bs-toggle="dropdown"
            >
              <i class="align-middle" data-feather="settings"></i>
            </a>

            <!-- Desktop avatar + name -->
            <a
              class="nav-link dropdown-toggle d-none d-sm-inline-block"
              href="#"
              data-bs-toggle="dropdown"
            >
              <!-- <img
                v-if="items.length === 0"
                :src="require('../assets/img/raqkie.jpg')"
                class="avatar img-fluid rounded me-1"
                alt="Charles Hall"
              /> -->
              <img
                v-if="items.length === 0"
                :src="require('../assets/img/ISU_LOGO.png')"
                class="avatar img-fluid rounded me-1"
                alt="Charles Hall"
              />

              <img
                v-else
                v-for="(item, index) in items"
                :key="index"
                :src="item.src || require('../assets/img/raqkie.jpg')"
                class="avatar img-fluid rounded me-1"
                alt="Charles Hall"
              />
              <span :class="['align-middle', isDark ? 'text-white' : 'text-dark']">
                {{ displayName }}
              </span>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="dropdown-menu dropdown-menu-end mt-1"
              :class="[isDark ? 'bg-dark text-white' : 'bg-white text-dark']"
            >
              <!-- <a class="dropdown-item" href="#">Profile</a>
              <a class="dropdown-item" href="#">Settings</a>
              <div class="dropdown-divider"></div> -->
              <a class="dropdown-item" @click="logout">Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
/* eslint-disable */
import "@mdi/font/css/materialdesignicons.css";
import { useTheme } from "vuetify";
import { computed } from "vue";
import axios from "axios";

export default {
  name: "AppBar",
  setup() {
    const theme = useTheme();

    const isDark = computed(() => theme.global.name.value === "dark");

    const toggleTheme = () => {
      theme.global.name.value = isDark.value ? "light" : "dark";
    };

    const themeClass = computed(() => `v-theme--${theme.global.name.value}`);

    return {
      isDark,
      toggleTheme,
      themeClass,
    };
  },

  data() {
    return {
      displayName: "",
      items: [],
    };
  },

  mounted() {
    //this.fetch();
    this.displayName = localStorage.getItem("DN") || "";
  },

  methods: {
    fetch() {
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

    initializeSidebarCollapse() {
      const sidebarElement = document.querySelector(".js-sidebar");
      if (sidebarElement) {
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
  },
};
</script>

<style scoped>
.indicator {
  position: absolute;
  top: 0;
  right: 0;
  width: 18px;
  height: 18px;
  background: red;
  color: white;
  border-radius: 50%;
  text-align: center;
  line-height: 18px;
  font-size: 12px;
}

.appbar-fixed {
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  z-index: 1030;
}
</style>
