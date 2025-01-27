"use strict";
const sliderPrincipaleInfooxJs = Vue.createApp({
    data() {
        return {
            app: document.getElementById('sliderPrincipaleInfooxJs'),
            images_url: [],
            tailleWidthEcran: window.innerWidth,
        };
    },
    methods: {
        changeImageSrc(){
            let images = Array.from(this.app.getElementsByTagName('img'));
            if(this.tailleWidthEcran <= 991){
                images.forEach((image)=>{
                    image.src = image.src.replace('pc', 'phone');
                });
            }else{
                images.forEach((image)=>{
                    image.src = image.src.replace('phone', 'pc');
                });
            }
        }
    },
    computed: {

    },

    mounted() {
        this.changeImageSrc();
        window.addEventListener('resize', ()=>{
            this.tailleWidthEcran = window.innerWidth;
            this.changeImageSrc()
        });
    },

});
sliderPrincipaleInfooxJs.mount("#sliderPrincipaleInfooxJs");