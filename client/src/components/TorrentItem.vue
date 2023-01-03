<script setup>
import TorrentLabel from "../components/TorrentLabel.vue";

const props = defineProps([
  "title",
  "size",
  "magnet",
  "seeders",
  "leechers",
  "source",
]);
</script>

<template>
  <div class="flex flex-1 flex-grow">
    <TorrentLabel :source="props.source" />
    <div
      class="flex flex-col flex-grow px-3 py-2 bg-neutral-200 rounded-r-md drop-shadow-sm"
    >
      <div class="flex justify-between">
        <p class="font-bold w-60 sm:w-max truncate">{{ props.title }}</p>
        <p class="text-sm font-bold">{{ props.size }}</p>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-xs text-neutral-500">
          Seeders: {{ props.seeders }} / Leechers: {{ props.leechers }}
        </span>
        <a
          :href="props.magnet"
          class="px-2 py-1 rounded-md text-white bg-neutral-300 hover:bg-sky-700 transition-all"
        >
          <i class="fa-solid fa-magnet"></i>
        </a>
      </div>
    </div>
  </div>
</template>
