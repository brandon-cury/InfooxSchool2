<template>
  <a :href="'/books/' + convertToSlug(examen.title) + '-e' + examen.id" class="card" style="width: 40px;">
    <div class="imgInfoox">
      <DefaultImageExamen :alt="examen.title" :src="examen.image"  :class="'card-img-top'" />
    </div>
    <div class="card-body">
      <h5 class="card-title oneLine">{{ examen.title }}</h5>
      <p class="card-text oneLine">Examen</p>
      <div class="d-flex justify-content-between">
        <div style="color: #D5B42E">
          <svg xmlns="http://www.w3.org/2000/svg" width="1.4rem" height="1.4rem" viewBox="0 0 128 128"><path fill="#fdd835" d="m68.05 7.23l13.46 30.7a7.05 7.05 0 0 0 5.82 4.19l32.79 2.94c3.71.54 5.19 5.09 2.5 7.71l-24.7 20.75c-2 1.68-2.91 4.32-2.36 6.87l7.18 33.61c.63 3.69-3.24 6.51-6.56 4.76L67.56 102a7.03 7.03 0 0 0-7.12 0l-28.62 16.75c-3.31 1.74-7.19-1.07-6.56-4.76l7.18-33.61c.54-2.55-.36-5.19-2.36-6.87L5.37 52.78c-2.68-2.61-1.2-7.17 2.5-7.71l32.79-2.94a7.05 7.05 0 0 0 5.82-4.19l13.46-30.7c1.67-3.36 6.45-3.36 8.11-.01"/><path fill="#ffff8d" d="m67.07 39.77l-2.28-22.62c-.09-1.26-.35-3.42 1.67-3.42c1.6 0 2.47 3.33 2.47 3.33l6.84 18.16c2.58 6.91 1.52 9.28-.97 10.68c-2.86 1.6-7.08.35-7.73-6.13"/><path fill="#f4b400" d="M95.28 71.51L114.9 56.2c.97-.81 2.72-2.1 1.32-3.57c-1.11-1.16-4.11.51-4.11.51l-17.17 6.71c-5.12 1.77-8.52 4.39-8.82 7.69c-.39 4.4 3.56 7.79 9.16 3.97"/></svg>
          4.5
        </div>
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" width="1.4rem" height="1.4rem" viewBox="0 0 32 32"><path fill="black" d="M16 2A14.173 14.173 0 0 0 2 16a14.173 14.173 0 0 0 14 14a14.173 14.173 0 0 0 14-14A14.173 14.173 0 0 0 16 2m8 15h-7v7h-2v-7H8v-2h7V8h2v7h7Z"/><path fill="none" d="M24 17h-7v7h-2v-7H8v-2h7V8h2v7h7z"/></svg>
        </div>

      </div>
    </div>

  </a>
</template>
<script setup>
import DefaultImageExamen from "../controllers/default-image-examen.vue";
import {convertToSlug} from "../../js/method/slug";

defineProps(["examen"]);
</script>
<style scoped>
.card{
  width: 150px !important;
  text-decoration: none;
  margin-left: 10px;
}
.card .imgInfoox{
  position: relative;
  width: 80%;
  height: 100px;
  overflow: hidden;
  margin: 5px auto;
}
.card .imgInfoox img{
  width:  100%;
  height: auto;
  max-height: 100px;
  object-fit: cover;
}
.card .card-body .card-title{
  color:#E31524;
  font-size: 1.44rem;
}
/* Tablette */
@media screen and (min-width: 993px) {
  .card{
    width: 250px !important;
    margin-left: 0px;
  }
  .card .imgInfoox{
    width: 80%;
    height: 150px;
    margin: 10px auto;
  }
  .card .imgInfoox img{
    max-height: 150px;
  }
}
/* endTablette */
/* Desktops */
@media screen and (min-width: 992px) {

}
/* endDesktops */
</style>