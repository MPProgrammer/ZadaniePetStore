document.addEventListener('DOMContentLoaded', () => {
    const statusSelect = document.getElementById('status');
    const tableBody = document.querySelector('#pets-table tbody');

    function renderLoading() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="3">Loading...</td>
            </tr>
        `;
    }

    function renderError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="3" style="color:red;">${message}</td>
            </tr>
        `;
    }

    function renderPets(pets) {
        tableBody.innerHTML = '';

        if (!pets.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3">No pets found</td>
                </tr>
            `;
            return;
        }

        pets.forEach(pet => {
            tableBody.innerHTML += `
                <tr>
                    <td>${pet.id}</td>
                    <td>${pet.name ?? '-'}</td>
                    <td>${pet.status}</td>
                </tr>
            `;
        });
    }

    function loadPets() {
        renderLoading();

        fetch(`/ajax/pets?status=${statusSelect.value}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error');
                }
                return response.json();
            })
            .then(data => {
                // LIMIT danych po stronie frontu (performance)
                renderPets(data.slice(0, 50));
            })
            .catch(() => {
                renderError('Error loading pets');
            });
    }

    statusSelect.addEventListener('change', loadPets);

    // pierwszy load
    loadPets();
});
