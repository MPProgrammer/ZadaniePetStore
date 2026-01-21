document.addEventListener('DOMContentLoaded', () => {
    const statusSelect = document.getElementById('status');
    const tableBody = document.querySelector('#pets-table tbody');
    const messageBox = document.getElementById('message');

    function showSuccess(msg) {
        messageBox.innerHTML = `<p class="notification is-success">${msg}</p>`;
    }

    function showError(msg) {
        messageBox.innerHTML = `<p class="notification is-danger">${msg}</p>`;
    }

    function renderLoading() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4">Loading...</td>
            </tr>
        `;
    }

    function renderError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" style="color:red;">${message}</td>
            </tr>
        `;
    }

    // function renderPets(pets) {
    //     tableBody.innerHTML = '';

    //     if (!pets.length) {
    //         tableBody.innerHTML = `
    //             <tr>
    //                 <td colspan="4">No pets found</td>
    //             </tr>
    //         `;
    //         return;
    //     }

    //     pets.forEach(pet => {
    //         tableBody.innerHTML += `
    //             <tr>
    //                 <td>${pet.id}</td>
    //                 <td>${pet.name ?? '-'}</td>
    //                 <td>${pet.status}</td>
    //             </tr>
    //         `;
    //     });
    // }

    function renderPets(pets) {
        tableBody.innerHTML = '';

        if (!pets.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4">No pets found</td>
                </tr>
            `;
            return;
        }

        pets.forEach(pet => {
            tableBody.innerHTML += `
                <tr data-id="${pet.id}">
                    <td class="column-id">${pet.id}</td>
                    <td class="column-name">${pet.name ?? '-'}</td>
                    <td class="column-status">${pet.status}</td>
                    <td class="column-actions">
                        <a class="button button-warning" href="/pets/${pet.id}/edit">Edit</a>
                        |
                        <button class="button button-red delete-btn" data-id="${pet.id}">
                            Delete
                        </button>
                    </td>
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
                // renderPets(data.slice(0, 50));
                renderPets(data);
            })
            // .catch(() => {
            //     renderError('Error loading pets');
            // });
            .catch(err => {
                // console.log(err);
                // console.log(err.error);
                if (err.errors && err.errors.status) {
                    renderError(err.errors.status[0]);
                }else if (err.error && typeof err.error === 'string') {
                    renderError(err.error);
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

    tableBody.addEventListener('click', e => {
        if (!e.target.classList.contains('delete-btn')) return;

        const id = e.target.dataset.id;

        if (!confirm(`Are you sure you want to delete pet #${id}?`)) {
            return;
        }

        // apiFetch(`/pets/${id}/deleteajax`, {
        //     method: 'POST'
        // })
        // .then(() => {
        //     showSuccess('Pet deleted successfully');
        //     loadPets(); // odśwież listę
        // })
        // .catch(err => {
        //     showError(err.error || 'Delete failed');
        // });

        

        // fetch(`/ajax/pets/${id}/delete`)
        // .then(() => {
        //     showSuccess('Pet deleted successfully');
        //     loadPets(); // odśwież listę
        // })
        // .catch(err => {
        //     showError(err.error || 'Delete failed');
        // });

        fetch(`/ajax/pets/${id}/delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error();
            return response.json();
        })
        .then(() => {
            showSuccess('Pet deleted successfully');
            loadPets();
        })
        .catch(() => {
            showError('Delete failed');
        });

    });

});
