import $ from 'jquery';
//pour les étoile
import 'font-awesome/css/font-awesome.css';
import { Fancybox } from "@fancyapps/ui";

document.addEventListener("turbo:load", () => {
    const app = Vue.createApp({
        data() {
        },
        methods: {

            payement(event) {
                event.preventDefault(); // Empêche l'action par défaut du clic
                const element = event.currentTarget; // Utiliser currentTarget pour cibler l'élément déclencheur correct

                // Récupérer tous les éléments <a> enfants du parent form
                const parentForm = element.closest('form');
                const childLinks = Array.from(parentForm.querySelectorAll('a'));

                // Ajouter la classe 'd-none' initialement
                childLinks.forEach(link => link.classList.add('d-none'));

                $.ajax({
                    url: '/cinetpay/action',
                    method: 'GET',
                    success: (response) => {
                        childLinks.forEach((link) => {
                            link.href = response;
                            link.classList.remove('d-none'); // Retirer la classe 'd-none' pour rendre les liens visibles
                        });
                    },
                    error: (error) => {
                        console.error(error);
                    }
                });
            },

            toggleClass(event, nameClasse){
                const element = event.target;
                element.classList.toggle(nameClasse);
            }

        },

        mounted() {
            Fancybox.bind('[data-fancybox="gallery"]', {
                // Your custom options for a specific gallery
            });
        },
    });
    app.mount("#bookPage");
});


//slider des astuces



