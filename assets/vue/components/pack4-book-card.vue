<template>
  <div class="cardBooks py-2">
    <div class=" container-fluid g-4 contenairCard">
      <h3 class="display-6 fw-bold oneLine">{{ name }}</h3>
      <div class="row mt-2 g-2">
        <div class="col col-6" v-for="(book, index) in props.books[0]" :key="index" @click="handleClick(index, true)">
          <a :href="'/accessBook/' + book.slug">
            <div class="imgContainer">
              <DefaultImageBook :path="book.path" :src="fetchImagePrincipale(book.images)" :alt="book.title" :class="'imgItem img-thumbnail'" />
            </div>
            <h4 class="twoLine">{{ book.title }}</h4>
          </a>
        </div>
      </div>
      <a href="#" class="voirPlus">Voir plus...</a>
    </div>
  </div>
</template>

<script setup>
import {ref, onMounted, computed, onUnmounted} from "vue";
import DefaultImageBook from "../controllers/default-image-book.vue";

const props = defineProps({
  books: {type: Array, required: true},
  name: {type: String, required: true},
});

//obtenir l'image principale d'un livre
const fetchImagePrincipale = (images) => {
  if (images && images.length > 0) {
    const imagesTrier = images.sort((a, b) => a.sort - b.sort);
    return imagesTrier[0].path;
  } else {
    return null;
  }
};
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
  width: 100%;
  height: 180px;
  overflow: hidden;
}
.imgContainer .imgItem {
  height: auto;
}

</style>
