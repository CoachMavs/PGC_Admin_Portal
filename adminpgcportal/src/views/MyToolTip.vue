<template>

<v-btn color="#1770d6" rounded="xl" @click="showModal()">Add Building</v-btn>

  <v-dialog v-model="dialog" persistent max-width="600px">
    <v-card>
      <v-card-title>
        Dialog Title
      </v-card-title>
      <v-card-text>
        Dialog content goes here...
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" @click="dialog = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>



<script>
export default {
  data() {
    return {
      dialog: false
    };
  },
methods: {
    showToolTip() {
      this.aaa = true; // Toggles the value of show
      console.log(this.aaa)
    },
    showModal() {

      this.dialog = true
    }
  
}
}

</script>

<style scoped>
/* You can customize the backdrop color here */
.v-overlay {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>