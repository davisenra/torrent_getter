<script setup>
const props = defineProps(["source"]);
</script>

<template>
  <div
    v-if="props.source === 'RARBG'"
    class="flex flex-col rounded-l-md justify-center items-center w-5 bg-blue-300 text-sm font-bold transform"
  >
    <p class="-rotate-90 tracking-widest w-max">RARBG</p>
  </div>
  <div
    v-if="props.source === 'YTS.MX'"
    class="flex flex-col rounded-l-md justify-center items-center w-5 bg-orange-300 text-sm font-bold transform"
  >
    <p class="-rotate-90 tracking-widest w-max">YTS</p>
  </div>
</template>
