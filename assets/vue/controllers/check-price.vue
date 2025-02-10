<template>
  <div class="d-flex flex-wrap justify-content-around my-2" >
    <div>
      <span ref="priceElement" class="bookPrice fw-bolder me-1 bg-danger text-white px-1">{{ calculatePrice(price) }} Fcfa</span>
      <span class="text-danger"> Le plus bas: <i class="text-decoration-line-through">{{ price }} Fcfa</i></span>
    </div>
    <div>
      <select name="achatTime" class="form-select form-select-sm" aria-label=".form-select-sm example" @change="fetchPrice($event)">
        <option value="3 jour" selected>3 jour</option>
        <option value="1 semaine">1 semaine</option>
        <option value="1 mois">1 mois</option>
        <option value="3 mois">3 mois</option>
        <option value="1 an">1 an</option>
      </select>
    </div>
  </div>
</template>
<script setup>

import $ from "jquery";
import {ref} from "vue";
import {calculatePrice} from "../../js/method/calculatePrice";

const props = defineProps({
  price: {type: Number, required: true},
});
const priceElement = ref(null);

const fetchPrice = (event) => {
  const selectedValue = event.target.value;
  $.ajax({
    url: '/asyn/book/price/session/data',
    method: 'GET',
    data: { time: selectedValue },
    success: (response) => {
      priceElement.value.innerHTML = response + ' Fcfa';
    },
    error: (error) => {
      console.error(error);
    }
  });
}
</script>

<style scoped>

</style>