<template>
  <div class="cardBooks py-2">
    <div class=" container-fluid g-4 contenairCard">
      <h3 class="display-6 fw-bold oneLine">{{ name }}</h3>
      <a :href="'/accessBook/' + elem.slug">
        <div class="imgContainer">
          <DefaultImageBook :path="elem.path" :src="srcImageSlide" :alt="elem.title" :class="'imgPrincipale'" />
        </div>
        <h4 class="mt-1 fw-bolder mt-2 twoLine">{{ elem.title }}</h4>
      </a>
      <div class="oneLine" v-if="elem.price">
        <span class="fw-bolder me-3 text-danger">{{ calculatePrice(elem.price) }} Fcfa</span>
        <span> Le plus bas: <i class="text-decoration-line-through">{{ elem.price }} Fcfa</i></span>
      </div>
      <div v-else>
        <span class="fw-bolder me-3 text-danger">Gratuit</span>
      </div>

      <div class="row mt-2">
        <div class="col col-3" v-for="(book, index) in books" :key="index" @click="handleClick(index, true)">

          <DefaultImageBook :src="fetchImagePrincipale(book.images)" :path="book.path" :alt="book.title" :class="{ 'border-highlight': index === highlightedIndex }" />
        </div>
      </div>
      <a href="#" class="voirPlus">Voir plus...</a>
    </div>
  </div>
</template>

<script setup>
import {ref, onMounted, computed, onUnmounted, watch} from "vue";
import { calculatePrice } from "../../js/method/calculatePrice";
import DefaultImageBook from "../controllers/default-image-book.vue";

const props = defineProps({
  books: {type: Array, required: true},
  name: {type: String, required: true},
  slide: {type: Boolean, default: true}
});
const books = ref([]);
const elem = ref(Object);
const highlightedIndex = ref(null);

const cycleBooks = () => {
  let index = highlightedIndex.value;
  index = (index + 1) % books.value.length;
  handleClick(index);
};
const intervalId = ref(null);
const startInterval = (time = 3000) => {
  intervalId.value = setInterval(cycleBooks, time);
   };
const stopInterval = () => { clearInterval(intervalId.value); };

//obtenir l'image principale d'un livre
const fetchImagePrincipale = (images) => {
    if (images && images.length > 0) {
      const imagesTrier = images.sort((a, b) => a.sort - b.sort);
      return imagesTrier[0].path;
    } else {
      return null;
    }
};
const srcImageSlide = computed(() => {
  return fetchImagePrincipale(elem.value.images);
});

onMounted(() => {
    books.value = props.books[0];
    elem.value = books.value[0];
    highlightedIndex.value = 0;
    if (props.slide) { startInterval(); }
});
onUnmounted(() => { stopInterval();  });// Nettoyer l'intervalle lors du démontage du composant
  const handleClick = (index, clicker = false) => {
    elem.value = books.value[index];
    highlightedIndex.value = index;
    stopInterval();
    if (props.slide) {
      startInterval(clicker ? 10000 : 3000);
    }
  }
</script>

<style scoped>
h3 {
  font-size: 2.074rem;
}

h4, h5 {
  font-size: 1.2rem;
}

.cardBooks {
  width: 100%;
  overflow: hidden;
  background: rgba(250, 250, 250, 1);
  margin: 0 auto;
  height: 550px;
}
.cardBooks .contenairCard{
  display: block;
  max-width: 450px;
}

img {
  width: 100%;
  height: auto;
  object-fit: cover;
}

.imgContainer {
  width: 60%;
  height: 250px;
  margin: 0 auto;
  overflow: hidden;
}

.imgContainer .imgPrincipale {
  display: block;
}

.imgItem {
  height: 90px;
}
.border-highlight {
  border: 2px solid red;
}
</style>
