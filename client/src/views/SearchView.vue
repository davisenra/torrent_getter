<script setup>
import { ref } from "vue";
import SearchBar from "../components/SearchBar.vue";
import TorrentItem from "../components/TorrentItem.vue";
import LoadingLoop from "../assets/icons/LoadingLoop.vue";

let torrents = ref(null);
const resultsFound = ref(0);
const isLoading = ref(false);
const requestError = ref(false);

const fetchResults = async (searchString) => {
  isLoading.value = true;
  const response = await fetch("http://localhost:8000/search", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ "search-string": searchString }),
  });

  if (response.status === 204 || response.status === 500) {
    isLoading.value = false;
    requestError.value = true;
    return;
  }

  const { data: torrentCollection } = await response.json();
  isLoading.value = false;
  torrents.value = torrentCollection;
  resultsFound.value = torrentCollection.length;
};
</script>

<template>
  <div class="container mx-auto flex justify-center items-center flex-col py-8">
    <SearchBar @search="fetchResults" />
    <div
      v-show="isLoading"
      class="flex justify-center items-center w-full max-w-4xl mt-6 px-3"
    >
      <LoadingLoop />
    </div>
    <Transition name="fade">
      <div
        v-show="!isLoading && (torrents !== null || requestError == true)"
        class="w-full max-w-4xl mt-6 px-3"
      >
        <h2 class="text-2xl font-bold">Results</h2>
        <p class="text-neutral-300 mb-3">
          {{ resultsFound }} results were found
        </p>
        <ul v-show="torrents !== null">
          <li
            v-for="(torrent, index) in torrents"
            :key="index"
            class="flex mb-3 gap-2"
          >
            <TorrentItem
              :title="torrent.title"
              :magnet="torrent.magnet"
              :size="torrent.size"
              :source="torrent.source"
              :seeders="torrent.seeders"
              :leechers="torrent.leechers"
            />
          </li>
        </ul>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
