<?php
// Memanggil header dari folder templates di root
require_once '../templates/header.php';
?>

<!-- Content Area -->
<main class="flex-1 p-6 overflow-y-auto">
    <!-- FORMULIR -->
    <div id="form-section" class="bg-white p-6 rounded-lg shadow-md">
        <h2 id="form-title" class="text-2xl font-semibold mb-6">Form Tambah Data Dokter</h2>

        <form id="dokter-form" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Input tersembunyi untuk menyimpan nama asli saat mode edit -->
            <input type="hidden" id="original-nama-dr" name="original_Nama_Dr">

            <div>
                <label for="no-poli" class="block mb-1 font-medium text-gray-700">No Poli</label>
                <input type="text" id="no-poli" name="No_Poli" required
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="md:col-span-3">
                <label for="nama-dr" class="block mb-1 font-medium text-gray-700">Nama Dokter</label>
                <input type="text" id="nama-dr" name="Nama_Dr" required
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="jam-praktek" class="block mb-1 font-medium text-gray-700">Jam Praktek</label>
                <input type="text" id="jam-praktek" name="Jam_Praktek"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="sesi" class="block mb-1 font-medium text-gray-700">Sesi</label>
                <select id="sesi" name="NoDisplay"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Sesi (Kosong)</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div>
                <label for="minim" class="block mb-1 font-medium text-gray-700">Minim</label>
                <input type="number" id="minim" name="Minim"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="maxim" class="block mb-1 font-medium text-gray-700">Maxim</label>
                <input type="number" id="maxim" name="Maxim"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="loket" class="block mb-1 font-medium text-gray-700">Loket</label>
                <input type="text" id="loket" name="Loket"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <input type="hidden" id="no-antri" name="NoAntri" value="0">
            <input type="hidden" id="no-urut" name="NoUrut" value="0">
            <div id="form-buttons" class="md:col-span-4 flex space-x-4">
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Tambah</button>
            </div>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div class="bg-white p-6 rounded-lg shadow-md mt-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h2 class="text-2xl font-semibold">Data Dokter</h2>
            <div class="w-full md:w-1/3">
                <input type="text" id="search-input" placeholder="Cari nama dokter..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-medium">No</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Nama Dokter</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Jam Praktek</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Minim</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Sesi</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Maxim</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dokter-table-body" class="bg-white"></tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const apiUrl = '../api/dokter_poli.php';
            const formSection = document.getElementById('form-section');
            const form = document.getElementById('dokter-form');
            const tableBody = document.getElementById('dokter-table-body');
            const formTitle = document.getElementById('form-title');
            const formButtons = document.getElementById('form-buttons');
            const searchInput = document.getElementById('search-input');

            let searchQuery = '';

            function connectWebSocket() {
                const socket = new WebSocket('ws://127.0.0.1:8080');
                socket.onopen = () => console.log("Koneksi WebSocket Dokter berhasil dibuat!");
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
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-10"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i></td></tr>`;
                try {
                    const response = await fetch(`${apiUrl}?search=${searchQuery}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        renderTable(result.data);
                    } else {
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">Gagal memuat data.</td></tr>`;
                    }
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">Terjadi kesalahan.</td></tr>`;
                }
            }

            function renderTable(data) {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">Data tidak ditemukan.</td></tr>`;
                    return;
                }
                let no = 1;
                data.forEach(dokter => {
                    const row = `
                    <tr class="border-t border-gray-200 hover:bg-gray-50">
                        <td class="py-2 px-4 text-sm align-middle">${no++}</td>
                        <td class="py-2 px-4 text-sm align-middle">${dokter.Nama_Dr}</td>
                        <td class="py-2 px-4 text-sm align-middle">${dokter.Jam_Praktek}</td>
                        <td class="py-2 px-4 text-sm align-middle">${dokter.Minim}</td>
                        <td class="py-2 px-4 text-sm align-middle">${dokter.NoDisplay || ''}</td>
                        <td class="py-2 px-4 text-sm align-middle">${dokter.Maxim}</td>
                        <td class="py-2 px-4 text-sm align-middle space-x-2">
                            <button data-action="edit" data-id="${dokter.Nama_Dr}" class="inline-block px-3 py-1 bg-yellow-400 text-yellow-900 rounded hover:bg-yellow-500 transition text-xs">Edit</button>
                            <button data-action="delete" data-id="${dokter.Nama_Dr}" class="inline-block px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs">Hapus</button>
                        </td>
                    </tr>
                `;
                    tableBody.innerHTML += row;
                });
            }

            let debounceTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchQuery = searchInput.value;
                    fetchDataAndRender();
                }, 300);
            });

            function resetForm() {
                form.reset();
                form.dataset.mode = 'add';
                document.getElementById('no-poli').readOnly = false;
                document.getElementById('original-nama-dr').value = '';
                formTitle.textContent = 'Form Tambah Data Dokter';
                formButtons.innerHTML = `<button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Tambah</button>`;
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

            tableBody.addEventListener('click', function (e) {
                const target = e.target;
                const action = target.dataset.action;
                const id = target.dataset.id;
                if (!action || !id) return;
                if (action === 'edit') handleEdit(id);
                else if (action === 'delete') handleDelete(id);
            });

            formButtons.addEventListener('click', function (e) {
                if (e.target.matches('button[data-action="cancel"]')) {
                    resetForm();
                }
            });

            async function handleEdit(id) {
                try {
                    const response = await fetch(`${apiUrl}?id=${encodeURIComponent(id)}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const result = await response.json();
                    if (result.status === 'success' && result.data) {
                        const data = result.data;
                        form.dataset.mode = 'edit';
                        formTitle.textContent = 'Form Edit Data Dokter';

                        document.getElementById('original-nama-dr').value = data.Nama_Dr;
                        document.getElementById('no-poli').value = data.No_Poli;
                        document.getElementById('nama-dr').value = data.Nama_Dr;
                        document.getElementById('jam-praktek').value = data.Jam_Praktek;
                        document.getElementById('sesi').value = data.NoDisplay || '';
                        document.getElementById('minim').value = data.Minim;
                        document.getElementById('maxim').value = data.Maxim;
                        document.getElementById('loket').value = data.Loket;

                        formButtons.innerHTML = `
                        <button type="submit" class="px-5 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Update</button>
                        <button type="button" data-action="cancel" class="px-5 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">Batal</button>
                    `;
                        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        Swal.fire('Error', result.message || 'Data dokter tidak ditemukan.', 'error');
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
                            const response = await fetch(`${apiUrl}?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
                            const res = await response.json();
                            if (res.status === 'success') {
                                Swal.fire('Dihapus!', 'Data dokter telah berhasil dihapus.', 'success');
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
    // Memanggil footer dari folder templates di root
    require_once '../templates/footer.php';
    ?>