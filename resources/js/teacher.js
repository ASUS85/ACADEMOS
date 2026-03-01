document.addEventListener('DOMContentLoaded', function () {

    const departmentSelect = document.getElementById('department');
    const filiereSelect = document.getElementById('filiere');

    if (!departmentSelect) return;

    departmentSelect.addEventListener('change', function () {

        fetch('/api/filieres/' + this.value)
            .then(response => response.json())
            .then(data => {

                filiereSelect.innerHTML = '<option value="">-- Sélectionner spécialité --</option>';

                data.forEach(filiere => {
                    filiereSelect.innerHTML += `
                        <option value="${filiere.id}">
                            ${filiere.name}
                        </option>
                    `;
                });

            });

    });

});
