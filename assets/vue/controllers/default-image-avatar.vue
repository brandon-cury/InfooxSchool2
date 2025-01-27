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
const debutUrlImg = ref('/images/avatar/');

const updateSrcValue = () => {
  if (!props.src) {
    srcValue.value = debutUrlImg.value + 'default.jpg';
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
/* Tablette */
@media screen and (min-width: 768px) {

}

/* endTablette */
/* Desktops */
@media screen and (min-width: 992px) {

}

/* endDesktops */
</style>
