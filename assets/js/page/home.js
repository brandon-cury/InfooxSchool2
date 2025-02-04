
"use strict";
import $ from 'jquery';

const app = Vue.createApp({
    data() {
        return {
        };
    },
    methods: {},
    computed: {

    },

    mounted() {
        $('.examen').find('.slider').slick({
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
        $('.examen').removeClass('d-none');
        $('.examen').addClass('d-block');
    },
});
app.mount("#homePage"); // conteneur html avec id="app"

//slider des astuces



$(document).ready(function() {
    //alert('jQuery est chargé et prêt à l\'emploi!');
});
