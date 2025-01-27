<template>
  <div class="comments">
    <div class="commentContainer border-bottom" :style="{ height: containerHeight }">
      <CommentsBookCard v-for="comment in comments" :key="comment.id" :comment="comment" :idUser="idUser" />
    </div>
    <div class="moreComment" @click="addPage">
      <span>Plus</span>
      <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24">
        <path fill="#fff" d="M7.41 8.58L12 13.17l4.59-4.59L18 10l-6 6l-6-6z"/>
      </svg>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import $ from 'jquery';
import CommentsBookCard from "../components/comments-book-card.vue";

const props = defineProps({
  numPageComments: { type: Number, required: true },
  idUser: { type: Number }
});
const page = ref(1);
const comments = ref([]);
const containerHeight = ref('400px');;

// Fonction pour chercher les commentaires
const fetchComments = (page) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: '/asyn/comments/book/data',
      method: 'GET',
      data: {
        page: page,
        limit: 6,
      },
      success: (response) => {
        resolve(response);
      },
      error: (error) => {
        console.error(error);
        reject(error);
      }
    });
  });
};

// Fonction pour ajouter la nouvelle page de commentaires
const addPage = async () => {
  containerHeight.value = '100%';
  if (page.value < props.numPageComments) {
    page.value += 1;
    const newComments = await fetchComments(page.value);
    comments.value = [...comments.value, ...newComments]; // Concaténer les nouveaux commentaires avec les anciens
    if(page.value >= props.numPageComments){
      document.querySelector('.moreComment').classList.add('d-none');
    }
  } else {
    document.querySelector('.moreComment').classList.add('d-none');
  }

};

// Charger les commentaires lors du montage du composant
onMounted(async () => {
  const initialComments = await fetchComments(page.value);
  comments.value = initialComments;
});

</script>

<style scoped>
.comments {
  position: relative;
  cursor: pointer;
}
.commentContainer {
  height: 400px;
  overflow: hidden;
}
.moreComment {
  margin: 0 auto;
  text-align: center;
  width: 100%;
  background: rgba(0, 0, 0, 0.2);
  overflow: hidden;
  transition: height 0.3s ease;
}
.moreComment:hover {
  background: rgba(0, 0, 0, 0.4);
}
@media screen and (min-width: 768px) {
  /* Tablette specific styles */
}
@media screen and (min-width: 992px) {
  /* Desktops specific styles */
}
</style>
