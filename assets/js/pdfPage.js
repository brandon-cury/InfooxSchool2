import $ from 'jquery';

$(document).ready(function() {

    if ($('#pdfPage').length) {
        function adjustSize() {
            const $viewer = $('#viewer');
            const $pElement = $('.pdfAffiche');

            const viewerHeight = $viewer.outerHeight();

            // Ajouter 100 pixels à la hauteur de la div .pdfAffiche
            $pElement.height(viewerHeight + 100);
        }

        // Ajuster la taille initiale
        adjustSize();

        // Utilisation de MutationObserver pour surveiller les changements de taille
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'attributes' && (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                    adjustSize();
                }
            });
        });

        const viewer = document.getElementById('viewer');
        observer.observe(viewer, {attributes: true});

        // Déclencher la fonction d'ajustement si nécessaire
        $(window).on('resize', function () {
            adjustSize();
        });

    }
});
