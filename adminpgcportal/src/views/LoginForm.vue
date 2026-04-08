<template>
  <div class="container">
    <v-card class="vcardmain" elevation="16" max-width="420">
      <v-progress-linear
        color="teal"
        indeterminate
        v-if="fetchLoading"
      ></v-progress-linear>
      <v-card-title class="custom-title"> Login </v-card-title>

      <div class="custom-container">
        <img :src="require('../assets/img/pgc.png')" draggable="false" class="imglogo" />
      </div>

      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                label="Username"
                variant="outlined"
                v-model="username"
                hide-details
                required
                @keyup.enter="login"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <v-text-field
                label="Password"
                variant="outlined"
                v-model="password"
                hide-details
                required
                type="password"
                @keyup.enter="login"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-banner
            v-if="showBanner"
            v-model="showBanner"
            color="error"
            icon="mdi-alert"
            single-line
          >
            {{ errorMessage }}
          </v-banner>

          <v-btn
            class="vbtn"
            color="rgb(13, 110, 253)"
            rounded="xl"
            @click="login"
            :loading="btnloading"
            >Sign-in</v-btn
          >
        </v-container>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import LoginForm from "./LoginForm.js";
export default LoginForm;
</script>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 0 16px;
}

.custom-title {
  height: 64px;
  background-color: rgb(13, 110, 253);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.vbtn {
  margin-top: 20px;
  width: 100%;
  height: 50px;
}

.custom-container {
  display: flex;
  justify-content: center;
  margin-top: 16px;
}

.imglogo {
  width: 30%;
}

@media (max-width: 600px) {
  .vcardmain {
    width: 100%;
  }

  .imglogo {
    width: 50%;
  }

  .vbtn {
    width: 100%;
  }
}
</style>
