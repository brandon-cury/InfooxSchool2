<template>
    <div>Hello {{ name }} {{ prenom }}!</div>
</template>

<script setup>
import {onMounted, ref} from "vue";

    defineProps({
        name: String
    });
    const prenom = ref("Brandon");
    onMounted(()=>{

        })
</script>
<style scoped>
div{
  color:red
}
</style>
