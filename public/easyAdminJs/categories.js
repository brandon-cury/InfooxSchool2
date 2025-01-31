// Inclure TomSelect via CDN
const script = document.createElement('script');
script.src = 'https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js';
document.head.appendChild(script);

script.onload = function() {
    document.addEventListener('DOMContentLoaded', function () {
        var sectionSelect = document.querySelector('#Bord_section');
        var filiereSelect = document.querySelector('#Bord_filiere');
        var classeSelect = document.querySelector('#Bord_classe');
        var matiereSelect = document.querySelector('#Bord_matiere');

        if (sectionSelect) {
            sectionSelect.addEventListener('change', function () {
                var selectedSections = Array.from(sectionSelect.selectedOptions).map(option => option.value);

                fetch('/filter-filieres', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ sections: selectedSections })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Détruire l'instance TomSelect existante pour les filières
                        if (filiereSelect.tomselect) {
                            filiereSelect.tomselect.destroy();
                        }

                        // Effacer les options existantes pour les filières
                        filiereSelect.innerHTML = '';
                        data.filieres.forEach(filiere => {
                            var option = document.createElement('option');
                            option.value = filiere.id;
                            option.textContent = filiere.name;
                            filiereSelect.appendChild(option);
                        });

                        // Réinitialiser TomSelect pour les filières
                        new TomSelect(filiereSelect, {
                            plugins: ['remove_button']
                        });

                        // Déclencher le changement des filières pour mettre à jour les classes
                        var event = new Event('change');
                        filiereSelect.dispatchEvent(event);
                    });
            });
        }

        if (filiereSelect) {
            filiereSelect.addEventListener('change', function () {
                var selectedFilieres = Array.from(filiereSelect.selectedOptions).map(option => option.value);

                fetch('/filter-classes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ filieres: selectedFilieres })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Détruire l'instance TomSelect existante pour les classes
                        if (classeSelect.tomselect) {
                            classeSelect.tomselect.destroy();
                        }

                        // Effacer les options existantes pour les classes
                        classeSelect.innerHTML = '';
                        data.classes.forEach(classe => {
                            var option = document.createElement('option');
                            option.value = classe.id;
                            option.textContent = classe.name;
                            classeSelect.appendChild(option);
                        });

                        // Réinitialiser TomSelect pour les classes
                        new TomSelect(classeSelect, {
                            plugins: ['remove_button']
                        });

                        // Déclencher le changement des classes pour mettre à jour les matières
                        var event = new Event('change');
                        classeSelect.dispatchEvent(event);
                    });
            });
        }

        if (classeSelect && matiereSelect) {
            classeSelect.addEventListener('change', function () {
                var selectedClasses = Array.from(classeSelect.selectedOptions).map(option => option.value);

                fetch('/filter-matieres', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ classes: selectedClasses })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Détruire l'instance TomSelect existante pour les matières
                        if (matiereSelect.tomselect) {
                            matiereSelect.tomselect.destroy();
                        }

                        // Effacer les options existantes pour les matières
                        matiereSelect.innerHTML = '';
                        data.matieres.forEach(matiere => {
                            var option = document.createElement('option');
                            option.value = matiere.id;
                            option.textContent = matiere.name;
                            matiereSelect.appendChild(option);
                        });

                        // Réinitialiser TomSelect pour les matières
                        new TomSelect(matiereSelect, {
                            plugins: ['remove_button']
                        });
                    });
            });
        }
    });
};
