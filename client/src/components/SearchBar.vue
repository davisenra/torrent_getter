<script setup>
import { ref, defineEmits } from "vue";
import SearchIcon from "../assets/icons/SearchIcon.vue";

const searchString = ref(null);
const emit = defineEmits(["search"]);
const searchAction = () => {
  emit("search", searchString.value);
  searchString.value = null;
};
</script>

<template>
  <form class="flex justify-center w-full" @submit.prevent="searchAction">
    <div class="mx-3 flex w-full justify-center">
      <input
        v-model="searchString"
        type="text"
        class="py-3 pl-3 rounded-l-md drop-shadow-sm w-full max-w-2xl"
        placeholder="Search"
      />
      <button
        class="px-3 bg-sky-800 text-white rounded-r-md drop-shadow-sm hover:bg-sky-900 transition-all"
      >
        <SearchIcon />
      </button>
    </div>
  </form>
</template>
