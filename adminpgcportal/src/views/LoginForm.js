/* eslint-disable */

import 'bootstrap';
import axios from 'axios';
export default {
  name: 'Login',

  data() {
    return {
      dialog: true,
      username: '',
      password: '',
      showBanner: false,
      errorMessage: '',
      fetchLoading: false,
      btnloading: false,
    };
  },
  mounted() {

  },
  methods: {


    checkAuthentication() {
      this.fetchLoading = true;
      this.btnloading = true;
      axios({
        method: 'post',
        url: process.env.VUE_APP_API + 'auth/me',
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('xxx')

        }
      }).then((resp) => {
        this.fetchLoading = false;
        this.btnloading = false;

 
      }).catch((err) => {
        this.fetchLoading = false;
        this.btnloading = false;
      })
    },
    login() {
      
      if (this.showBanner) {
        this.showBanner = false;
      }
      this.fetchLoading = true;
      this.btnloading = true;
      axios({
        
        method: 'post',
        url: process.env.VUE_APP_API + 'auth/login',
        data: {
          username: this.username,
          password: this.password
        }
      }).then((resp) => {
        
        localStorage.setItem('xxx', resp.data.access_token);
 
        this.$router.push('/Dashboard')
        this.fetchLoading = false;
        this.btnloading = false;
      }).catch((err) => {
        // alert("Invalid credentials")
        this.errorMessage = "Invalid credentials";
        this.showBanner = true;
        setTimeout(() => {
          this.showBanner = false;
        }, 3000);
        this.fetchLoading = false;
        this.btnloading = false;
      })
      
    },
  },
};
