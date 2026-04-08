<template>
  <footer
    class="footer py-3"
    :class="[isDark ? 'bg-dark text-white' : 'bg-white text-dark']"
  >
    <div class="container-fluid">
      <div class="row text-muted">
        <div class="col-6 text-start">
          <strong>
            Copyright &copy; 2025
            <a href="#" :class="[isDark ? 'text-white' : 'text-dark']">
              Provincial Government of Cagayan </a
            >.
          </strong>
          All rights reserved.
        </div>
        <div class="col-6 text-end">
          <ul class="list-inline mb-0">
            <li class="list-inline-item"><b>Version</b> 2.0</li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { useTheme } from "vuetify";
import { computed } from "vue";

export default {
  name: "FooTer",
  setup() {
    const theme = useTheme();
    const isDark = computed(() => theme.global.name.value === "dark");

    return {
      isDark,
    };
  },
};
</script>
