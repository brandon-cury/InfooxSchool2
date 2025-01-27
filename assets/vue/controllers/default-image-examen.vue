<template>
  <img :src="srcValue" :alt="props.alt" :class="props.class" />
</template>

<script setup>
import {onMounted, ref, watch} from "vue";

const props = defineProps({
  src: String,
  alt: {type: String, required: true},
  class: String,
});

const srcValue = ref('');
const debutUrlImg = ref('/examens/');
const updateSrcValue = () => {
  if (!props.src) {
    srcValue.value = debutUrlImg.value + 'default.png';
  } else {
    srcValue.value = debutUrlImg.value + props.src;
  }
};
onMounted(() => {
  updateSrcValue();
});

watch(() => props.src, (newValue, oldValue) => {
  updateSrcValue();
});
</script>

<style scoped>

</style>
