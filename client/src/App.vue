<script setup>
import CoreHeader from "./components/HomeHeader.vue";
import SearchViewVue from "./views/SearchView.vue";
</script>

<template>
  <div class="h-screen bg-neutral-50">
    <header>
      <CoreHeader />
    </header>
    <main>
      <SearchViewVue />
    </main>
  </div>
</template>
