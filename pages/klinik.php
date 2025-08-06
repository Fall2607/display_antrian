<?php
require_once '../templates/header.php';
?>

<!-- Content Area -->
<main class="flex-1 p-6 overflow-y-auto">
    <!-- Form Card -->
    <div id="form-section" class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 id="form-title" class="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Form Tambah Data Klinik</h2>

        <form id="klinik-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block mb-2 font-semibold text-gray-700">NO KLINIK</label>
                <input type="text" id="no-klinik-display" readonly
                    class="w-full rounded border-gray-300 px-4 py-2 bg-gray-100 cursor-not-allowed" />
                <input type="hidden" id="no-klinik-edit" name="NO_KLINIK">
            </div>
            <div>
                <label for="nama-klinik" class="block mb-2 font-semibold text-gray-700">Nama Klinik</label>
                <input type="text" id="nama-klinik" name="NAMA_KLINIK" required
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div class="md:col-span-2">
                <label for="image-url" class="block mb-2 font-semibold text-gray-700">Image URL</label>
                <input type="text" id="image-url" name="image"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div id="form-buttons" class="md:col-span-2 flex justify-end gap-4">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Tambah</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Data Klinik</h2>
            <div class="flex w-full md:w-auto items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="rows-per-page" class="text-sm font-medium text-gray-700">Baris:</label>
                    <select id="rows-per-page" class="px-2 py-1 border border-gray-300 rounded-md text-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                    </select>
                </div>
                <div class="w-full md:w-64">
                    <input type="text" id="search-input" placeholder="Cari nama klinik..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">No</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Id Klinik</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Nama Klinik</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Image</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="klinik-table-body" class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>
        <div id="pagination-container" class="flex justify-end items-center mt-4"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const apiUrl = '../api/klinik.php';
            const formSection = document.getElementById('form-section');
            const form = document.getElementById('klinik-form');
            const tableBody = document.getElementById('klinik-table-body');
            const formTitle = document.getElementById('form-title');
            const formButtons = document.getElementById('form-buttons');
            const searchInput = document.getElementById('search-input');
            const paginationContainer = document.getElementById('pagination-container');
            const rowsPerPageSelect = document.getElementById('rows-per-page');

            let currentPage = 1;
            let searchQuery = '';
            let rowsPerPage = parseInt(rowsPerPageSelect.value, 10);

            function connectWebSocket() {
                const wsHost = `ws://${window.location.hostname}:8080`;
                const socket = new WebSocket(wsHost);
                socket.onopen = () => console.log("Koneksi WebSocket Klinik berhasil dibuat!");
                socket.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    if (data.event === 'data_updated') {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Data sedang diperbarui...', showConfirmButton: false, timer: 1500 });
                        fetchDataAndRender();
                    }
                };
                socket.onclose = () => setTimeout(connectWebSocket, 2000);
                socket.onerror = (error) => console.error(`Error WebSocket: ${error.message}`);
            }

            async function fetchDataAndRender() {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-10"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i></td></tr>`;
                try {
                    const response = await fetch(`${apiUrl}?page=${currentPage}&search=${searchQuery}&limit=${rowsPerPage}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        renderTable(result.data);
                        renderPagination(result.pagination);
                    } else {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Gagal memuat data.</td></tr>`;
                    }
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Terjadi kesalahan.</td></tr>`;
                }
            }

            function renderTable(data) {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Data tidak ditemukan.</td></tr>`;
                    return;
                }
                let no = (currentPage - 1) * rowsPerPage + 1;
                data.forEach(klinik => {
                    const row = `
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm">${no++}</td>
                        <td class="py-3 px-4 text-sm">${klinik.NO_KLINIK}</td>
                        <td class="py-3 px-4 text-sm font-semibold">${klinik.NAMA_KLINIK}</td>
                        <td class="py-3 px-4 text-sm">
                            ${klinik.image ? `<img src="${klinik.image}" alt="Image Klinik" class="w-16 h-16 object-cover rounded-md" onerror="this.onerror=null;this.src='https://placehold.co/64x64/E2E8F0/4A5568?text=Error';"/>` : `<span class="text-gray-400">No Image</span>`}
                        </td>
                        <td class="py-3 px-4 text-sm space-x-2">
                            <button data-action="edit" data-id="${klinik.NO_KLINIK}" class="text-yellow-500 hover:text-yellow-700"><i class="fas fa-pencil-alt"></i></button>
                            <button data-action="delete" data-id="${klinik.NO_KLINIK}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                `;
                    tableBody.innerHTML += row;
                });
            }

            function renderPagination(pagination) {
                const { page, totalPages } = pagination;
                paginationContainer.innerHTML = '';
                if (totalPages <= 1) return;
                const createButton = (p, text, disabled = false) => {
                    const activeClass = p === page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300';
                    return `<button onclick="changePage(${p})" ${disabled ? 'disabled' : ''} class="px-3 py-1 mx-1 text-sm rounded ${activeClass} ${disabled ? 'opacity-50 cursor-not-allowed' : ''}">${text}</button>`;
                };
                paginationContainer.innerHTML += createButton(page - 1, 'Sebelumnya', page <= 1);
                for (let i = 1; i <= totalPages; i++) paginationContainer.innerHTML += createButton(i, i);
                paginationContainer.innerHTML += createButton(page + 1, 'Berikutnya', page >= totalPages);
            }

            window.changePage = (newPage) => {
                if (newPage > 0) {
                    currentPage = newPage;
                    fetchDataAndRender();
                }
            };

            let debounceTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchQuery = searchInput.value;
                    currentPage = 1;
                    fetchDataAndRender();
                }, 300);
            });

            rowsPerPageSelect.addEventListener('change', () => {
                rowsPerPage = parseInt(rowsPerPageSelect.value, 10);
                currentPage = 1;
                fetchDataAndRender();
            });

            function resetForm() {
                form.reset();
                form.dataset.mode = 'add';
                formTitle.textContent = 'Form Tambah Data Klinik';
                formButtons.innerHTML = `<button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all flex items-center gap-2"><i class="fas fa-save"></i><span>Tambah</span></button>`;
                document.getElementById('no-klinik-display').value = 'Otomatis';
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const mode = form.dataset.mode || 'add';
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                const method = (mode === 'edit') ? 'PUT' : 'POST';

                try {
                    const response = await fetch(apiUrl, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
                    const result = await response.json();
                    if (result.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: result.message, icon: 'success', timer: 2000, showConfirmButton: false });
                        resetForm();
                    } else {
                        Swal.fire({ title: 'Gagal!', text: result.message, icon: 'error' });
                    }
                } catch (error) {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengirim data.', 'error');
                }
            });

            tableBody.addEventListener('click', (e) => {
                const button = e.target.closest('button');
                if (!button) return;
                const action = button.dataset.action;
                const id = button.dataset.id;
                if (!action || !id) return;
                if (action === 'edit') handleEdit(id);
                else if (action === 'delete') handleDelete(id);
            });

            formButtons.addEventListener('click', (e) => {
                if (e.target.closest('button[data-action="cancel"]')) {
                    resetForm();
                }
            });

            async function handleEdit(id) {
                try {
                    const response = await fetch(`${apiUrl}?id=${id}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const result = await response.json();
                    if (result.status === 'success' && result.data) {
                        const data = result.data;
                        form.dataset.mode = 'edit';
                        formTitle.textContent = 'Form Edit Data Klinik';

                        document.getElementById('no-klinik-edit').value = data.NO_KLINIK;
                        document.getElementById('no-klinik-display').value = data.NO_KLINIK;
                        document.getElementById('nama-klinik').value = data.NAMA_KLINIK;
                        document.getElementById('image-url').value = data.image;

                        formButtons.innerHTML = `
                        <button type="button" data-action="cancel" class="px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow-md hover:bg-gray-600 transition-all flex items-center gap-2"><i class="fas fa-times"></i><span>Batal</span></button>
                        <button type="submit" class="px-6 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 transition-all flex items-center gap-2"><i class="fas fa-save"></i><span>Update</span></button>
                    `;
                        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        Swal.fire('Error', result.message || 'Data klinik tidak ditemukan.', 'error');
                    }
                } catch (error) {
                    console.error('Error fetching data for edit:', error);
                    Swal.fire('Error', 'Gagal mengambil data untuk diedit. Periksa konsol.', 'error');
                }
            }

            function handleDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?', text: "Data yang dihapus tidak dapat dikembalikan!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' });
                            const res = await response.json();
                            if (res.status === 'success') {
                                Swal.fire('Dihapus!', 'Data klinik telah berhasil dihapus.', 'success');
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        } catch (error) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    }
                });
            }

            fetchDataAndRender();
            connectWebSocket();
            resetForm();
        });
    </script>

    <?php
    require_once '../templates/footer.php';
    ?>