<template>
  <li>
    <a :href="'/chapitre/' + slug">
      <div class="m">
        <h2>{{ title }}</h2>
      </div>
    </a>
    <div class="k">
      <a :href="'/chapitre/' + slug">
        <i class="fa fa-eye" id="i"></i>
        <i class="fa fa-eye" id="i"></i>
      </a>
    </div>
    <div class="l">
      <a :href="'/chapitre/' + slug + '?=exercices'">
        <i class="fa fa-list" id="i"></i>
        <i class="fa fa-list" id="i"></i>
      </a>
    </div>

  </li>

  <li class="q">lecon 1: les triade</li>
</template>

<script setup>
import {onMounted, ref, watch} from "vue";


const props = defineProps({
  title: {type: String, required: true},
  slug: {type: String, required: true},
  categorie: {type: Boolean, required: true},
  exercice: {type: Boolean, required: true},
  access: {type: Boolean, required: true},
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
