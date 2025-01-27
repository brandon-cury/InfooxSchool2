<template>
  <div ref="slideBooks" class="slideBooks d-none container-fluid g-0 g-md-3 g-lg-5 my-2 my-lg-4">
    <div class="bg-white py-2 py-lg-4">
      <h2 class="pb-2 pb-lg-3 text-danger">{{ props.title }}</h2>
      <div class="slider overflow-hidden">
        <BookSlideCard v-for="book in books" :key="book.id" :book="book" :title="props.title" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, nextTick } from 'vue';
import BookSlideCard from "../components/book-slide-card.vue";
import $ from 'jquery';

const props = defineProps({
  filtres: { type: Array, required: false },
  title: { type: String, required: true },
  sort: { type: Array, required: false },
  limit: { type: Number, default: 25 },
});

const books = ref([]);
const slideBooks = ref(null);

onMounted(() => {
  $.ajax({
    url: '/asyn/slide/books/data',
    method: 'GET',
    data: {
      filtres: props.filtres,
      limit: props.limit,
      sort: props.sort
    },
    success: (response) => {
      books.value = response;
      if (response.length !== 0) {
        nextTick(() => {
          $(slideBooks.value).find('.slider').slick({
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 7,
            centerMode: true,
            variableWidth: true,
            arrows: true,
            responsive: [
              {
                breakpoint: 993,
                settings: {
                  dots: false,
                  infinite: true,
                  speed: 300,
                  slidesToScroll: 4,
                  centerMode: true,
                  variableWidth: true,
                  arrows: false,
                }
              },
            ]
          });
          slideBooks.value.classList.remove('d-none');
          slideBooks.value.classList.add('d-block');
        });
      }
    },
    error: (error) => {
      console.error(error);
    }
  });
});
</script>

<style scoped>
/* Styles spécifiques à tes besoins */
</style>
