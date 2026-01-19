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

        fetch(`/ajax/pets?status=${statusSelect.value}`, {
                headers: {
                    'Accept': 'application/json',
                    // 'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                return data;
            })
            .then(data => {
                // LIMIT danych po stronie frontu (performance)
                renderPets(data.slice(0, 50));
            })
            // .catch(() => {
            //     renderError('Error loading pets');
            // });
            .catch(err => {
                // console.log(err);
                // console.log(err.error);
                if (err.errors && err.errors.status) {
                    renderError(err.errors.status[0]);
                } else {
                    renderError('Unexpected error');
                }
            });

        // fetch(`/ajax/pets?status=${statusSelect.value}`, {
        //     headers: {
        //         'Accept': 'application/json'
        //     }
        // })
        // .then(async response => {
        //     const data = await response.json();

        //     if (!response.ok) {
        //         // TU rzucamy JSON, nie Error()
        //         throw data;
        //     }

        //     return data;
        // })
        // .then(data => {
        //     renderPets(data.slice(0, 50));
        // })
        // .catch(err => {
        //     // console.log(err);

        //     if (err.error) {
        //         renderError(err.error);
        //     } else {
        //         renderError('Unexpected error');
        //     }
        // });
    }

    statusSelect.addEventListener('change', loadPets);

    // pierwszy load
    loadPets();
});
