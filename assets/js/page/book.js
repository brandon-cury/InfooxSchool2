
"use strict";
import $ from 'jquery';
//importation de trix editor
import 'trix/dist/trix.css';
import 'trix/dist/trix.umd';
//pour les étoile
import 'font-awesome/css/font-awesome.css';
//$(document).ready(function() {alert('eee');});

//importation de @fancyapps pour l'afficharge des images de gallerie sur la page book
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import {nextTick} from "vue";
Fancybox.bind('[data-fancybox="gallery"]', {
    // Your custom options for a specific gallery
});

const app = Vue.createApp({
    data() {
        return {
            price_elem: null,
        };
    },
    methods: {
        addBasket(event, book_id){
            $.ajax({
                url: '/asyn/basket/add/book/data',
                method: 'GET',
                data: {
                    book: book_id
                },
                success: (response) => {
                    const state = response;
                    if(state === 'exist'){
                        this.afficheAlertBasket('Ce livre existe déjà dans votre panier', false);
                    }else if(state === false){
                        this.afficheAlertBasket('Veuillez vous connecter', false);
                    }else if(state === true){
                        this.afficheAlertBasket('Livre ajouter au panier', true);
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
        },
        afficheAlertBasket(text, style){
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
        },
        toggleClass(event, nameClasse){
            const element = event.target;
            element.classList.toggle(nameClasse);
        },
        fetchPrice(event) {
            const selectedValue = event.target.value;
            $.ajax({
                url: '/asyn/book/price/session/data',
                method: 'GET',
                data: { time: selectedValue },
                success: (response) => {
                    this.price_elem.innerHTML = response + ' Fcfa';
                },
                error: (error) => {
                    console.error(error);
                }
            });
        },


        payement(event) {
            event.preventDefault(); // Empêche l'action par défaut du clic
            const element = event.currentTarget; // Utiliser currentTarget pour cibler l'élément déclencheur correct
            console.log(element);

            // Récupérer tous les éléments <a> enfants du parent form
            const parentForm = element.closest('form');
            const childLinks = Array.from(parentForm.querySelectorAll('a'));

            // Ajouter la classe 'd-none' initialement
            childLinks.forEach(link => link.classList.add('d-none'));

            $.ajax({
                url: '/cinetpay/action',
                method: 'GET',
                success: (response) => {
                    console.log(response);
                    console.log(childLinks);
                    childLinks.forEach((link) => {
                        link.href = response;
                        link.classList.remove('d-none'); // Retirer la classe 'd-none' pour rendre les liens visibles
                        console.log(link);
                    });
                },
                error: (error) => {
                    console.error(error);
                }
            });
        },

    },
    computed: {

    },

    mounted() {
        this.price_elem = document.querySelector('.bookPrice');
    },
});
app.mount("#bookPage"); // conteneur html avec id="app"

//slider des astuces



