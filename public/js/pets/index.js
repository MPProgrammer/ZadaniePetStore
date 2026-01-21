document.addEventListener('DOMContentLoaded', () => {
    // DOM references
    const statusSelect = document.getElementById('status');
    const tableBody = document.querySelector('#pets-table tbody');
    const messageBox = document.getElementById('message');

    // Show success notification
    function showSuccess(message) {
        messageBox.innerHTML = `
            <div class="notification is-success">${message}</div>
        `;
    }

    // Show error notification
    function showError(message) {
        messageBox.innerHTML = `
            <div class="notification is-danger">${message}</div>
        `;
    }

    // Render loading state inside the table
    function renderLoading() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4">Loading...</td>
            </tr>
        `;
    }

    // Render error state inside the table
    function renderTableError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4">${message}</td>
            </tr>
        `;
    }

    /**
     * Generic fetch wrapper for JSON endpoints
     */
    function apiFetch(url, options = {}) {
        return fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {})
            },
            ...options
        })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw data;
                return data;
            });
    }

    // Render pets table rows
    function renderPets(pets) {
        if (!pets.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4">No pets found</td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = pets.map(pet => `
            <tr data-id="${pet.id}">
                <td class="column-id">${pet.id}</td>
                <td class="column-name">${pet.name ?? '-'}</td>
                <td class="column-status">${pet.status}</td>
                <td class="column-actions">
                    <a class="button button-warning" href="/pets/${pet.id}/edit">
                        Edit
                    </a>
                    <button class="button button-red delete-btn" data-id="${pet.id}">
                        Delete
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Load pets list from backend (filtered by status)
    function loadPets() {
        renderLoading();

        apiFetch(`/ajax/pets?status=${statusSelect.value}`)
            .then(renderPets)
            .catch(err => {
                if (err.errors?.status) {
                    renderTableError(err.errors.status[0]);
                } else if (typeof err.error === 'string') {
                    renderTableError(err.error);
                } else {
                    renderTableError('Unexpected error');
                }
            });
    }

    // Reload pets when status filter changes
    statusSelect.addEventListener('change', loadPets);

    // Initial load
    loadPets();

    // Handle delete action using event delegation
    tableBody.addEventListener('click', e => {
        if (!e.target.classList.contains('delete-btn')) {
            return;
        }

        const id = e.target.dataset.id;

        if (!confirm(`Are you sure you want to delete pet #${id}?`)) {
            return;
        }

        apiFetch(`/ajax/pets/${id}/delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            }
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
