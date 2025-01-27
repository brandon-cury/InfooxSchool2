<template>
    {{ reform() }}
</template>

<script setup>
import {onMounted, ref} from "vue";

const props = defineProps({
  all_user: {type: Int32Array, required:true},
});

const numbAlert = ref(0);
const reform = ()=>{
  if(props.all_user < 1000) return props.all_user;
  else if(props.all_user <= 999999) return (props.all_user/1000).toFixed(2) + ' K';
  return (props.all_user/1000000).toFixed(2) + ' M';
}

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