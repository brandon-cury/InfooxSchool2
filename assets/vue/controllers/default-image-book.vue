<template>
  <img :src="srcValue" :alt="props.alt" :class="props.class" />
</template>

<script setup>
import {onMounted, ref, watch} from "vue";

const props = defineProps({
  src: String,
  path: String,
  class: String,
  alt: {type: String, required: true},
});

const srcValue = ref('');
const debutUrlImg = ref('/bords/test/images/');

const updateSrcValue = () => {
  if (!props.src || !props.path) {
    const defaultImages = ['default1.jpg', 'default2.jpg'];
    srcValue.value = debutUrlImg.value + defaultImages[Math.floor(Math.random() * defaultImages.length)];
  } else {
    srcValue.value = '/bords/' + props.path + '/images/' + props.src;
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
