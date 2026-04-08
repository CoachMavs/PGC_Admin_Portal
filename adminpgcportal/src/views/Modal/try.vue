<template>
  <h1 class="card-header pb-3">Head Description</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
  </div>

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */

import axios from "axios";
import "bootstrap";
import MySnackBar from "@/components/MySnackBar.vue";

export default {
  name: "Mavs",
  components: {
    MySnackBar,
  },
  data() {
    return {
      fetchLoading: false,
      // add your reactive data properties here
    };
  },
  methods: {
    // add your functions here
  },
  mounted() {
    // runs after component is mounted
  },
  beforeUnmount() {
    // cleanup before component is destroyed
  },
};
</script>

<style scoped>
/* Add your scoped styles here */
</style>
