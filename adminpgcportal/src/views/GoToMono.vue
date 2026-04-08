<template>
  <iframe
    :src="iframeUrl"
    width="100%"
    height="100%"
    frameborder="0"
    style="min-height: 100vh"
  ></iframe>
</template>

<script setup>
import { ref, onMounted } from "vue";

const iframeUrl = ref("");

onMounted(() => {
  const token = localStorage.getItem("xxx");

  if (!token) {
    alert("Missing token");
    return;
  }

  iframeUrl.value =
    "https://tasks.cagayan.gov.ph/auth/redirect?token=$792|h3mhGJhYNsAjFXxhmrS4aVfIGzV7VHpSvPY2vV9ze1dc9da5";
});
</script>
