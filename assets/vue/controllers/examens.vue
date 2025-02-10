<template>
    <div ref="examen" class="examen d-none">
      <div class="examensSlider slider container-fluid g-0 g-lg-5">
        <ExamenCard v-for="examen in examens" :examen="examen" />
      </div>
    </div>

</template>

<script setup>
import {onMounted, ref, nextTick} from "vue";

import $ from 'jquery';
import ExamenCard from "../components/examen-card.vue";

const examen = ref(null);
const examens = ref([]);
let isVisible = ref(false);
onMounted(()=>{
  $.ajax({
    url: '/examen/data',
    method: 'GET',
    success: (response) => {
       examens.value = response;
       nextTick(()=>{
         if(examen.value){
           $(examen.value).find('.slider').slick({
             dots: false,
             infinite: true,
             speed: 300,
             slidesToShow: 6,
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
           examen.value.classList.remove('d-none');
           examen.value.classList.add('d-block');
         }
       });
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