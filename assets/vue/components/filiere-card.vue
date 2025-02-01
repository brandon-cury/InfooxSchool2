<template>
  <div class="col col-6 col-lg-3">
    <a :href="'/books/' + convertToSlug(filiere.title) + '-f' + filiere.id">
      <default-image-filiere :src="filiere.image"  :alt="filiere.title" />
      <h3 class="oneLine">{{ filiere.title }}</h3>
    </a>
  </div>
</template>
<script setup>
import {convertToSlug} from "../../js/method/slug";
import DefaultImageFiliere from "./default-image-filiere.vue";

defineProps(["filiere"]);
</script>
<style scoped>
  img{
    width: 100%;
    height:80px;
    object-fit: cover;
  }
</style>