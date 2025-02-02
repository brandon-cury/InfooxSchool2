<template>
  <a href="/basket" class="text-decoration-none me-2 me-lg-0 position-relative">
    <svg xmlns="http://www.w3.org/2000/svg" width="2rem" height="2rem" viewBox="0 0 48 48"><g fill="none"><path d="M39 32H13L8 12h36z"/><path stroke="#FBFAFA" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M3 6h3.5L8 12m0 0l5 20h26l5-20z"/><circle cx="13" cy="39" r="3" stroke="#FBFAFA" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"/><circle cx="39" cy="39" r="3" stroke="#FBFAFA" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"/></g></svg>
    <span ref="elemCount" class="elemCountBasket z-3 d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
      {{ count }}
      <span class="visually-hidden">unread messages</span>
    </span>
  </a>
</template>

<script setup>
import {nextTick, onMounted, ref} from "vue";
import $ from "jquery";
const count = ref(null);
const elemCount = ref(null);
const alertCount = (numb) =>{
  if(numb > 99){
    return '99+';
  }
  return numb;
}
const addBasket = ()=>{
  alert('bien');
}
onMounted(() => {
  $.ajax({
    url: '/asyn/basket/count/book/data',
    method: 'GET',
    success: (response) => {
      count.value = alertCount(response);
      nextTick(() => {
        if(count.value){
          elemCount.value.classList.remove('d-none');
          elemCount.value.classList.add('d-block');
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

</style>