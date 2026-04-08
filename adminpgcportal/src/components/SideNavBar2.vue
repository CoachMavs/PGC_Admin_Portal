<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <nav class="bg-dark text-white p-3 vh-100" style="width: 250px;">
      <ul class="nav flex-column">
        <!-- Employers Section -->
        <li class="nav-item">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-pen"></i> EMPLOYERS
          </a>
        </li>

        <!-- Job Postings with Dropdown -->
        <li class="nav-item">
          <button
            class="btn text-white w-100 d-flex justify-content-between align-items-center"
            @click="toggleCollapse"
          >
            <span>
              <i class="bi bi-grid"></i> JOB POSTINGS
            </span>
            <i
              :class="collapsed ? 'bi bi-caret-left' : 'bi bi-caret-down'"
            ></i>
          </button>
          <div :class="['collapse', { show: !collapsed }]" id="jobPostings">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a href="#" class="nav-link text-white">
                  <i class="bi bi-circle"></i> Approved Job Posting
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link text-white">
                  <i class="bi bi-circle"></i> Job Posting for Approval
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Job Seekers Section -->
        <li class="nav-item">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-person"></i> JOB SEEKERS
          </a>
        </li>

        <!-- Reports Section (Placeholder) -->
        <li class="nav-item">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-file-earmark-text"></i> REPORTS
          </a>
        </li>
      </ul>
    </nav>

    <!-- Main Content Area -->
    <div class="p-4 flex-grow-1">
      <h1>Main Content</h1>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import axios from 'axios';
import { useRoute } from 'vue-router';
import feather from 'feather-icons';
import SimpleBar from 'simplebar';
import 'bootstrap-icons/font/bootstrap-icons.css';
export default {

  name: 'App',
  components: {},
  data() {
    return {
      collapsed: true,

      items: [
        {
          id: 1,
          title: 'Applications :',
          children: [
            { id: 2, title: 'Calendar : app' },
            { id: 3, title: 'Chrome : app' },
            { id: 4, title: 'Webstorm : app' },
          ],
        },
        {
          id: 5,
          title: 'Documents :',
          children: [
            {
              id: 6,
              title: 'vuetify :',
              children: [
                {
                  id: 7,
                  title: 'src :',
                  children: [
                    { id: 8, title: 'index : ts' },
                    { id: 9, title: 'bootstrap : ts' },
                  ],
                },
              ],
            },
            {
              id: 10,
              title: 'material2 :',
              children: [
                {
                  id: 11,
                  title: 'src :',
                  children: [
                    { id: 12, title: 'v-btn : ts' },
                    { id: 13, title: 'v-card : ts' },
                    { id: 14, title: 'v-window : ts' },
                  ],
                },
              ],
            },
          ],
        },
        {
          id: 15,
          title: 'Downloads :',
          children: [
            { id: 16, title: 'October : pdf' },
            { id: 17, title: 'November : pdf' },
            { id: 18, title: 'Tutorial : html' },
          ],
        },
        {
          id: 19,
          title: 'Videos :',
          children: [
            {
              id: 20,
              title: 'Tutorials :',
              children: [
                { id: 21, title: 'Basic layouts : mp4' },
                { id: 22, title: 'Advanced techniques : mp4' },
                { id: 23, title: 'All about app : dir' },
              ],
            },
            { id: 24, title: 'Intro : mov' },
            { id: 25, title: 'Conference introduction : avi' },
          ],
        },
      ],
    };
  },
  setup() {
    const route = useRoute();

    const isActive = (routeName) => {
      return route.name === routeName;
    };

    return { isActive };
  },
  mounted() {
    this.initializeSimplebar();
    try {
      feather.replace();
    } catch (e) {
      console.log('You might have made a typo with one of the feather icons');
      console.log(e);
    }

  },
  methods: {
    toggleCollapse() {
      this.collapsed = !this.collapsed;
    },
    logout() {
      axios({
        name: process.env.VUE_APP_API + 'auth/logout',
        method: 'post',
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('xxx')
        }
      }).then((resp) => {

      }).catch((err) => {

      }).finally(() => {
        localStorage.removeItem('xxx')
        this.$router.push({ name: 'Login' }).then(() => {
          document.body.style.backgroundColor = 'white';
          window.location.reload();
        });

      })
    },
    goToApprovedEmployers() {
      this.$router.push({ name: 'Employers' });
    },
    goToApprovedJobPosting() {
      this.$router.push({ name: 'ApprovedJobPosting' });
    },
    goToApprovedJobPosting() {
      this.$router.push({ name: 'ApprovedJobPosting' });
    },
    goToJobPostingForApproval() {
      this.$router.push({ name: 'JobPostingForApproval' });
    },
    goToMeCost() {
      this.$router.push({ name: 'MeCost' });
    },
    goToOpCost() {
      this.$router.push({ name: 'OpCost' });
    },
    goToModBuildingClass() {
      this.$router.push({ name: 'ModBuildingClass' });
    },
    goToBuildingList() {
      this.$router.push({ name: 'BuildingList' });
    },
    goToBuildingClass() {
      this.$router.push({ name: 'BuildingClass' });
    },
    goToDashboard() {
      this.$router.push({ name: 'Dashboard' });
    },
    goToBuildingOpeMain() {
      this.$router.push({ name: 'BuildingOpe&Main' });
    },
    initializeSimplebar() {
      const simplebarElement = document.getElementsByClassName('js-simplebar')[0];
      if (simplebarElement) {
        const simplebarInstance = new SimpleBar(simplebarElement);

        const sidebarDropdowns = document.querySelectorAll('.js-sidebar [data-bs-parent]');

        sidebarDropdowns.forEach((link) => {
          link.addEventListener('shown.bs.collapse', () => {
            simplebarInstance.recalculate();
          });
          link.addEventListener('hidden.bs.collapse', () => {
            simplebarInstance.recalculate();
          });
        });
      }
    },
  },
};
</script>

<style scoped>
/* Default active sidebar item style */
.sidebar .sidebar-item.active>a {
  background-color: #343a40;
  color: #ffffff;
}

/* Style for dropdown menu items */
.sidebar .sidebar-item .collapse .nav-link {
  padding-left: 2rem;
  color: #adb5bd;
}

.sidebar .sidebar-item .collapse .nav-link:hover {
  color: #ffffff;
  background-color: #495057;
}

.nav-link {
  font-size: 14px;
  padding: 10px 0;
}

.collapse.show {
  background-color: rgba(255, 255, 255, 0.1);
}
</style>
