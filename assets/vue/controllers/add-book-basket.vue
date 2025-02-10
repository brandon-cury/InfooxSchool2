<template>
  <div class="addBasket mt-3">
    <button  @click="addBasket($event)" type="button" class="btn btn-dark position-relative me-4">
      <svg xmlns="http://www.w3.org/2000/svg" width="2rem" height="2rem" viewBox="0 0 48 48"><g fill="none"><path d="M39 32H13L8 12h36z"></path><path stroke="#FBFAFA" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M3 6h3.5L8 12m0 0l5 20h26l5-20z"></path><circle cx="13" cy="39" r="3" stroke="#FBFAFA" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></circle><circle cx="39" cy="39" r="3" stroke="#FBFAFA" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></circle></g></svg>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">+<span class="visually-hidden">unread messages</span></span>
    </button>
    <span class="afficheAlert"></span>
  </div>
</template>
<script setup>
  import $ from "jquery";

  const props = defineProps({
    book_id: {type: Number, required: true},
  });

  const addBasket = (event) =>{
    $.ajax({
      url: '/asyn/basket/add/book/data',
      method: 'GET',
      data: {
        book: props.book_id
      },
      success: (response) => {
        const state = response;
        if(state === 'exist'){
          afficheAlertBasket('Ce livre existe déjà dans votre panier', false);
        }else if(state === false){
          afficheAlertBasket('Veuillez vous connecter', false);
        }else if(state === true){
          afficheAlertBasket('Livre ajouter au panier', true);
          const baskets = Array.from(document.querySelectorAll('.elemCountBasket'));
          baskets.forEach((basket)=>{
            let number = parseInt(basket.textContent, 10);
            number +=1;
            basket.textContent= number;
            basket.classList.remove('d-none');
          })
        }

        /*
        count.value = alertCount(response);
        nextTick(() => {
            elemCount.value.classList.remove('d-none');
            elemCount.value.classList.add('d-block');
        });

         */

      },
      error: (error) => {
        console.error(error);
      }
    });
  }
  const afficheAlertBasket = (text, style) =>{
    const elem = document.querySelector('.addBasket');
    const alertElem = elem.querySelector('.afficheAlert');
    alertElem.textContent = text;
    alertElem.classList.remove('d-none');
    if (style === false) {
      alertElem.classList.remove('text-success');
      alertElem.classList.add('text-danger');
      setTimeout(() => {
        alertElem.textContent = '';
        alertElem.classList.add('d-none');
      }, 5000); // 5000 ms = 5 secondes
    }
    else{
      alertElem.classList.add('text-success');
      alertElem.classList.remove('text-danger');
      setTimeout(() => {
        alertElem.textContent = '';
        alertElem.classList.add('d-none');
        elem.classList.add('d-none');
      }, 2000);
    }
  }


</script>

<style scoped>

</style>