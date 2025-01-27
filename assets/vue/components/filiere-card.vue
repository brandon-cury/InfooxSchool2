<template>
  <div class="col col-6 col-lg-3">
    <a href="#">
      <img src="/filieres/image.png" style="width: 100%;" :alt="filiere.title">
      <h3 class="oneLine">{{ filiere.title }}</h3>
    </a>
  </div>
</template>
<script setup>
defineProps(["filiere"]);
</script>
<style scoped>

</style>