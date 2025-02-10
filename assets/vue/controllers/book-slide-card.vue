<template>
  <a :href="'/accessBook/' + bookObject.slug" class="card rounded-0" style="width: 40px;">
      <DefaultImageBook v-if="imageAffiche" :path="bookObject.path" :src="images[0].path" :alt="bookObject.title" :class="'card-img-top rounded-0 mb-2'" />
        <DefaultImageBook v-else :alt="bookObject.title" :class="'card-img-top rounded-0 mb-2'" />
    <div class="card-body p-0">


      <div v-if="bookObject.price" class="card-text oneLine">
        <span class="fw-bolder me-1 bg-danger text-white px-1">{{ calculatePrice(bookObject.price) }} Fcfa</span>
        <span class="text-danger"> Le plus bas: <i class="text-decoration-line-through">{{ bookObject.price }} Fcfa</i></span>
      </div>
      <div v-else class="card-text">
        <span class="fw-bolder me-3 text-danger">Gratuit</span>
      </div>

      <h4 class="card-title mt-2 twoLine">{{ bookObject.title }}</h4>
      <div class="d-flex justify-content-between">

        <div v-if="bookObject.star > 0" style="color: #D5B42E">
          <svg xmlns="http://www.w3.org/2000/svg" width="1.4rem" height="1.4rem" viewBox="0 0 128 128"><path fill="#fdd835" d="m68.05 7.23l13.46 30.7a7.05 7.05 0 0 0 5.82 4.19l32.79 2.94c3.71.54 5.19 5.09 2.5 7.71l-24.7 20.75c-2 1.68-2.91 4.32-2.36 6.87l7.18 33.61c.63 3.69-3.24 6.51-6.56 4.76L67.56 102a7.03 7.03 0 0 0-7.12 0l-28.62 16.75c-3.31 1.74-7.19-1.07-6.56-4.76l7.18-33.61c.54-2.55-.36-5.19-2.36-6.87L5.37 52.78c-2.68-2.61-1.2-7.17 2.5-7.71l32.79-2.94a7.05 7.05 0 0 0 5.82-4.19l13.46-30.7c1.67-3.36 6.45-3.36 8.11-.01"/><path fill="#ffff8d" d="m67.07 39.77l-2.28-22.62c-.09-1.26-.35-3.42 1.67-3.42c1.6 0 2.47 3.33 2.47 3.33l6.84 18.16c2.58 6.91 1.52 9.28-.97 10.68c-2.86 1.6-7.08.35-7.73-6.13"/><path fill="#f4b400" d="M95.28 71.51L114.9 56.2c.97-.81 2.72-2.1 1.32-3.57c-1.11-1.16-4.11.51-4.11.51l-17.17 6.71c-5.12 1.77-8.52 4.39-8.82 7.69c-.39 4.4 3.56 7.79 9.16 3.97"/></svg>
          {{ bookObject.star }}
        </div>
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" width="1.4rem" height="1.4rem" viewBox="0 0 32 32"><path fill="black" d="M16 2A14.173 14.173 0 0 0 2 16a14.173 14.173 0 0 0 14 14a14.173 14.173 0 0 0 14-14A14.173 14.173 0 0 0 16 2m8 15h-7v7h-2v-7H8v-2h7V8h2v7h7Z"/><path fill="none" d="M24 17h-7v7h-2v-7H8v-2h7V8h2v7h7z"/></svg>
        </div>

      </div>
    </div>

  </a>
</template>
<script setup>
import {onMounted, ref} from "vue";
import { calculatePrice } from "../../js/method/calculatePrice";
import DefaultImageBook from "./default-image-book.vue";

const props = defineProps({
  book: {type: Array, required: true}
});
const images = ref([]);
const imageAffiche = ref(false);
const bookObject = ref({});

onMounted(async() => {
  if(typeof props.book === 'string'){
    bookObject.value = JSON.parse(props.book);
  }else{
    bookObject.value = props.book;
  }
  await fetchImages();
});
const fetchImages = async () => {
    if (bookObject.value.images && bookObject.value.images.length > 0) {
      images.value = bookObject.value.images.sort((a, b) => a.sort - b.sort);
      if(images.value[0].path){
        imageAffiche.value =  true;
      }


    } else {
      //console.log('Aucune image à trier');
    }
};
</script>
<style scoped>
.card{
  width: 185px !important;
  text-decoration: none;
  margin-left: 10px;
}
.card img {
  width: 100%;
  display: block;
  height: 220px;
  object-fit: cover;
}
.card .card-body .card-title{

}
/* Tablette */
@media screen and (min-width: 993px) {
}
/* endTablette */
/* Desktops */
@media screen and (min-width: 992px) {
  .card{
    width: 250px !important;
    margin-left: 0px;
  }
  .card img {
    height: 300px;
  }
}
/* endDesktops */
</style>