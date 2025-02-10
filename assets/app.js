import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap';
//importation du slider slick
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
//require('jquery'); //importation de jquery car slick.js en a besoin v2.2.0
import 'slick-carousel/slick/slick';

//imoirtation des fichier js de composant a utiliser dans toute l'application
import './js/navbar2';
import './js/sliderPrincipale';


//importation du css trix editor
import 'trix/dist/trix.css';
import 'trix/dist/trix.umd';

//importation de @fancyapps pour l'afficharge des images de gallerie sur la page book
import "@fancyapps/ui/dist/fancybox/fancybox.css";

//importation des icone awesome pour l'afficharger des étoiles
import 'font-awesome/css/font-awesome.css';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import { registerVueControllerComponents } from '@symfony/ux-vue';
registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));
