<template>
  <div class="col col-6 col-lg-3">
    <a :href="'/books/' + convertToSlug(filiere.title) + '-f' + filiere.id">
      <img src="/filieres/image.png" style="width: 100%;" :alt="filiere.title">
      <h3 class="oneLine">{{ filiere.title }}</h3>
    </a>
  </div>
</template>
<script setup>
import {convertToSlug} from "../../js/method/slug";

defineProps(["filiere"]);
</script>
<style scoped>

</style>