// Inclure TomSelect via CDN
const scriptCollection = document.createElement('script');
scriptCollection.src = 'https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js';
document.head.appendChild(scriptCollection);

scriptCollection.onload = function() {
    document.addEventListener('DOMContentLoaded', function () {
        var editorSelect = document.querySelector('#Bord_editor');
        var collectionSelect = document.querySelector('#Bord_collection');

        function fetchCollections(editor) {
            fetch('/admin/filter-collections', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ editor: editor })
            })
                .then(response => response.json())
                .then(data => {
                    // Détruire l'instance TomSelect existante pour les collections
                    if (collectionSelect.tomselect) {
                        collectionSelect.tomselect.destroy();
                    }

                    // Effacer les options existantes pour les collections
                    collectionSelect.innerHTML = '';
                    data.collections.forEach(collection => {
                        var option = document.createElement('option');
                        option.value = collection.id;
                        option.textContent = collection.name;
                        collectionSelect.appendChild(option);
                    });

                    // Réinitialiser TomSelect pour les collections
                    new TomSelect(collectionSelect, {
                        plugins: ['remove_button']
                    });
                });
        }

        if (editorSelect) {
            editorSelect.addEventListener('change', function () {
                var selectedEditor = editorSelect.value;
                fetchCollections(selectedEditor);
            });

            // Exécuter la recherche lorsque la page se charge
            var initialEditor = editorSelect.value;
            if (initialEditor) {
                fetchCollections(initialEditor);
            }
        }
    });
}
