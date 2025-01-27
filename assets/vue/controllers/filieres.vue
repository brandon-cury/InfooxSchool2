<template>
  <div class="container-fluid g-0 g-lg-5 filieres">
    <div class="navbar2Infoox">
      <div class="containerInfoox">
        <ul>
          <li class="nolienkInfoox">
            Filières :
          </li>
          <li>
            <a @click="getElement" class="linkJs active" data-bs-toggle="collapse" href="#nouvelles" role="button" aria-expanded="false" aria-controls="collapseExample">
              Nouvelles
            </a>
          </li>
          <li>
            <a @click="getElement" class="linkJs" data-bs-toggle="collapse" href="#populaires" role="button" aria-expanded="false" aria-controls="collapseExample">
              Populaires
            </a>
          </li>
          <li>
            <a @click="getElement" class="linkJs" data-bs-toggle="collapse" href="#meilleures" role="button" aria-expanded="false" aria-controls="collapseExample">
              Meilleures
            </a>
          </li>

        </ul>
      </div>
    </div>
    <div class="filiereContainer container-fluid g-1 g-lg-5 h-auto">
      <div class="collapse show infooxFiliereContains" id="nouvelles">
        <div class="container-fluid g-3">
          <div class="row">

            <filiere-card v-for="filiere in nouvelles" :filiere="filiere" />

          </div>
        </div>
      </div>

      <div class="collapse infooxFiliereContains" id="populaires">
        <div class="container-fluid g-3">
          <div class="row">

            <filiere-card v-for="filiere in populaires" :filiere="filiere" />

          </div>
        </div>
      </div>

      <div class="collapse infooxFiliereContains" id="meilleures">
        <div class="container-fluid g-3">
          <div class="row">

            <filiere-card v-for="filiere in meilleures" :filiere="filiere" />

          </div>
        </div>
      </div>

      <a href="#" class="voirPlus">Voir plus...</a>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import $ from "jquery";
import FiliereCard from "../components/filiere-card.vue";

defineProps({

});
const filieres = ref([]);
const nouvelles = ref([]);
const populaires = ref([]);
const meilleures = ref([]);

const trieNouvellesF = ()=>{
  let nouvellesF = [...filieres.value].sort((a, b) => {
    return new Date(a.createdAt) - new Date(b.createdAt);
  });
  nouvelles.value = nouvellesF.slice(0, 12);
}
const triePopulairesF = ()=>{
  let populairesF = [...filieres.value].sort((a, b) => {
    return  b.all_user - a.all_user;
  });
  populaires.value = populairesF.slice(0, 12);
}
const trieMeilleuresF = ()=>{
  let meilleuresF = [...filieres.value].sort((a, b) => {
    return  a.sort - b.sort;
  });
  meilleures.value = meilleuresF.slice(0, 12);
}

onMounted(()=>{
  $.ajax({
    url: '/filiere/data',
    method: 'GET',
    success: (response) => {
      filieres.value = response;
      trieNouvellesF();
      triePopulairesF();
      trieMeilleuresF();
    },
    error: (error) => {
      console.error(error);
    }
  });
  const app = document.querySelector('.filieres');
  const container = app.querySelector('.filiereContainer');
  const infooxFiliereContains = container.querySelector('.infooxFiliereContains');
  setTimeout(()=>{
    container.style.height = infooxFiliereContains.offsetHeight + 40 + 'px';
  }, 2000);
  window.addEventListener('resize', ()=>{
    container.style.height = infooxFiliereContains.offsetHeight + 40 + 'px';
  });

});


const getElement = (event)=>{
  const link = event.target;
  const app = document.querySelector('.filieres');

  let links = Array.from(app.getElementsByClassName('linkJs'));
  links.forEach((element)=>{
    element.classList.remove('active');
  });
  link.classList.add('active');

  let items= Array.from(app.querySelectorAll('.infooxFiliereContains'));
  items.forEach((item)=>{
    if(link.href.includes(item.id)){
      item.classList.add('show');
    }else{
      item.classList.remove('show');
    }
  });
  //activeElement();
}
</script>
<style scoped>
</style>
