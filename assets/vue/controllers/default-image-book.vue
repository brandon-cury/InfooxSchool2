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
const debutUrlImg = ref('/bords/test/images/');

const updateSrcValue = () => {
  if (!props.src || !props.src.includes('bords/')) {
    const defaultImages = ['default1.jpg', 'default2.jpg'];
    srcValue.value = debutUrlImg.value + defaultImages[Math.floor(Math.random() * defaultImages.length)];
  } else {
    srcValue.value = props.src;
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
