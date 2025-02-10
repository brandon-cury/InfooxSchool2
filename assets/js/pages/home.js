"use strict";
import $ from 'jquery';
document.addEventListener("turbo:render", () => {
    const app = Vue.createApp({
        data() {
            return {
            };
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
    app.mount("#homePage");
});
