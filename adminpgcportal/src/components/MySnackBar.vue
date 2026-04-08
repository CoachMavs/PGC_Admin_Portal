<template>
  <v-snackbar v-model="snackbar" :timeout="timeout" :color="color">
    {{ text }}

    <template v-slot:actions>
      <v-btn :append-icon="color" variant="text" @click="snackbar = false">
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>
<script>
/* eslint-disable */
export default {
  name: 'MySnackBar',
  data: () => ({
    snackbar: false,
    text: '',
    timeout: 2000,
    color: 'blue',
  }),
  methods: {
    showSuccessMessage(str) {
      this.text = str;
      this.color = 'green-darken-4';
      this.snackbar = true;
    },
    showInfoMessage(str) {
      this.text = str;
      this.color = 'light-blue';
      this.snackbar = true;
    },
    showErrorMessage(str) {
      this.text = str;
      this.color = 'red-darken-4';
      this.snackbar = true;
    }
  }
}
</script>