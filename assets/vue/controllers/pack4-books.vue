<template>
  <div class="container-fluid g-0 g-md-3 g-lg-5 my-2 my-lg-4">
    <div class="row g-2 g-lg-3">
      <div class="col col-12 col-md-6 col-lg-3">
        <Pack4BookSlideCard v-if="keys.length && valeurs.length" :name="keys[0]" :books="valeurs[0]" />
      </div>
      <div class="col col-12 col-md-6 col-lg-3">
        <Pack4BookCard v-if="keys.length && valeurs.length" :name="keys[1]" :books="valeurs[1]" />
      </div>
      <div class="col col-12 col-md-6 col-lg-3">
        <Pack4BookSlideCard v-if="keys.length && valeurs.length" :name="keys[2]" :books="valeurs[2]" :slide="false" />
      </div>
      <div class="col col-12 col-md-6 col-lg-3">
        <Pack4BookCard v-if="keys.length && valeurs.length" :name="keys[3]" :books="valeurs[3]" />
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import $ from 'jquery';
import Pack4BookSlideCard from "../components/pack4-book-slide-card.vue";
import Pack4BookCard from "../components/pack4-book-card.vue";
const props = defineProps({ categories: Object });

const books = ref([]);
const keys = ref([]);
const valeurs = ref([]);

const splitBooks = (response) => {
  const tempKeys = [];
  const tempValues = [];
  Object.keys(response).forEach((key, index) => {
    tempKeys.push(key.slice(0, -2));
    tempValues.push(response[key]);
  });
  keys.value = tempKeys;
  valeurs.value = tempValues;
};

onMounted(() => {
  $.ajax({
    url: '/asyn/pack4/books/data',
    method: 'GET',
    data: { categories: props.categories },
    success: (response) => {
      books.value = response;
      splitBooks(response);
    },
    error: (error) => {
      console.error(error);
    }
  });
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