<template>
  <div class="comment">
    <div class="d-flex mt-4">
      <DefaultImageAvatar :src="comment.user.avatar" :class="'img-fluid rounded-circle'" :alt="comment.user.first_name" :style="'width: 40px; height: 40px; margin-right: 10px;'" />
      <div class="border-bottom w-100">
        <h5>{{ formattedDate(comment.created_at) }} - {{ comment.user.first_name }} <i v-if="!comment.is_published" class="text-danger"> - Non publié</i> </h5>
        <div class="textEditor twoLine" @click="toggleClass($event, 'twoLine')" v-html="comment.content"></div>
        <p class="my-2">
          <small>Evaluation de {{ comment.user.first_name }}:
            <span class="rating">
              <span v-for="i in 5" :key="i">
                <template v-if="i <= comment.rating">
                  <svg xmlns="http://www.w3.org/2000/svg" width="1.4rem" height="3rem" viewBox="0 0 24 24">
                    <path fill="#dada10" d="m12 18.26l-7.053 3.948l1.575-7.928L.588 8.792l8.027-.952L12 .5l3.385 7.34l8.027.952l-5.934 5.488l1.575 7.928z"/>
                  </svg>
                </template>
                <template v-else>
                <svg xmlns="http://www.w3.org/2000/svg" width="1.4rem" height="3rem" viewBox="0 0 24 24">
                  <path fill="black" d="m12 18.26l-7.053 3.948l1.575-7.928L.588 8.792l8.027-.952L12 .5l3.385 7.34l8.027.952l-5.934 5.488l1.575 7.928zm0-2.292l4.247 2.377l-.948-4.773l3.573-3.305l-4.833-.573l-2.038-4.419l-2.039 4.42l-4.833.572l3.573 3.305l-.948 4.773z"/>
                </svg>
                </template>
              </span>
          </span>
          </small>
        </p>
        <button
            v-if="comment.user.id === idUser"
            type="button"
            :data-id="comment.id"
            class="btn btn-warning btn-sm"
        >
          Modifier
        </button>
      </div>
    </div>
  </div>

</template>
<script setup>
import DefaultImageAvatar from "../controllers/default-image-avatar.vue";
import {formattedDate} from "../../js/method/formateDate";

const props = defineProps({
  comment: {type: Object, required:true},
  idUser: {type: Int8Array},
});

const toggleClass = (event, nameClasse)=>{
  const element = event.target;
  element.classList.toggle(nameClasse);
}
</script>
<style scoped>
</style>