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

        // Fonction utilitaire pour mettre à jour un select avec conservation des valeurs valides
        function updateSelect(select, newData, valueKey = 'id', labelKey = 'name') {
            if (!select || !select.tomselect) return;

            const currentValues = select.tomselect.getValue();
            const newValuesSet = new Set(newData.map(item => item[valueKey].toString()));
            const validValues = currentValues.filter(value => newValuesSet.has(value.toString()));

            select.tomselect.destroy();
            select.innerHTML = '';

            newData.forEach(item => {
                var option = document.createElement('option');
                option.value = item[valueKey];
                option.textContent = item[labelKey];
                select.appendChild(option);
            });

            const newTomSelect = new TomSelect(select, {
                plugins: ['remove_button']
            });

            if (validValues.length > 0) {
                newTomSelect.setValue(validValues);
            }

            return newTomSelect;
        }

        // Fonctions pour chaque niveau de filtrage
        function updateFilieres(selectedSections) {
            return fetch('/filter-filieres', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ sections: selectedSections })
            })
                .then(response => response.json())
                .then(data => {
                    updateSelect(filiereSelect, data.filieres);
                    // Retourner les filières sélectionnées pour la cascade
                    return Array.from(filiereSelect.selectedOptions).map(option => option.value);
                });
        }

        function updateClasses(selectedFilieres) {
            return fetch('/filter-classes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ filieres: selectedFilieres })
            })
                .then(response => response.json())
                .then(data => {
                    updateSelect(classeSelect, data.classes);
                    // Retourner les classes sélectionnées pour la cascade
                    return Array.from(classeSelect.selectedOptions).map(option => option.value);
                });
        }

        function updateMatieres(selectedClasses) {
            return fetch('/filter-matieres', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ classes: selectedClasses })
            })
                .then(response => response.json())
                .then(data => {
                    updateSelect(matiereSelect, data.matieres);
                });
        }

        // Initialisation des TomSelect
        function initializeTomSelects() {
            [sectionSelect, filiereSelect, classeSelect, matiereSelect].forEach(select => {
                if (select && !select.tomselect) {
                    new TomSelect(select, {
                        plugins: ['remove_button']
                    });
                }
            });
        }

        // Initialisation de la cascade de requêtes
        async function initializeFilters() {
            initializeTomSelects();

            // Récupérer les valeurs initiales sélectionnées
            const selectedSections = Array.from(sectionSelect.selectedOptions).map(option => option.value);

            if (selectedSections.length > 0) {
                const selectedFilieres = await updateFilieres(selectedSections);
                if (selectedFilieres.length > 0) {
                    const selectedClasses = await updateClasses(selectedFilieres);
                    if (selectedClasses.length > 0) {
                        await updateMatieres(selectedClasses);
                    }
                }
            }
        }

        // Event listeners
        if (sectionSelect) {
            sectionSelect.addEventListener('change', function() {
                const selectedSections = Array.from(sectionSelect.selectedOptions).map(option => option.value);
                updateFilieres(selectedSections)
                    .then(selectedFilieres => updateClasses(selectedFilieres))
                    .then(selectedClasses => updateMatieres(selectedClasses));
            });
        }

        if (filiereSelect) {
            filiereSelect.addEventListener('change', function() {
                const selectedFilieres = Array.from(filiereSelect.selectedOptions).map(option => option.value);
                updateClasses(selectedFilieres)
                    .then(selectedClasses => updateMatieres(selectedClasses));
            });
        }

        if (classeSelect) {
            classeSelect.addEventListener('change', function() {
                const selectedClasses = Array.from(classeSelect.selectedOptions).map(option => option.value);
                updateMatieres(selectedClasses);
            });
        }

        // Lancer l'initialisation au chargement de la page
        initializeFilters();
    });
};