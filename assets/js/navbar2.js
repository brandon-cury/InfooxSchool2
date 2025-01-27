"use strict";
const navbar2InfooxJs = Vue.createApp({
    data() {
        return {
            app: document.getElementById('navbar2InfooxJs'),

        };
    },
    methods: {
        getElement(event){
            const link = event.target.href;
            let items= Array.from(this.app.querySelectorAll('.collapse'));
            items.forEach((item)=>{
                if( ('#' + item.id) !== link){
                   item.classList.remove('show');
                }
            });

        }
    },
    computed: {},

    mounted() {
    },
});
navbar2InfooxJs.mount("#navbar2InfooxJs");
