<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tone.js untuk efek suara -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.7.77/Tone.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
        }

        .marquee-text {
            animation: marquee 20s linear infinite;
        }

        @keyframes marquee {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(-100%);
            }
        }

        /* Animasi saat nilai diperbarui */
        .value-update {
            animation: pulse-yellow 1.5s ease-out;
        }

        @keyframes pulse-yellow {
            0% {
                transform: scale(1);
                color: inherit;
            }

            50% {
                transform: scale(1.1);
                color: #FBBF24;
            }

            /* Tailwind yellow-400 */
            100% {
                transform: scale(1);
                color: inherit;
            }
        }
    </style>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.getItem('color-theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white transition-colors duration-300">

    <div class="min-h-screen flex flex-col p-4 sm:p-6 lg:p-8">
        <header class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4 flex-1">
                <label for="sesi-select" class="text-xl font-medium text-gray-700 dark:text-gray-300">Pilih
                    Sesi:</label>
                <select id="sesi-select"
                    class="bg-white border border-gray-300 text-gray-900 text-lg rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="1">Sesi 1</option>
                    <option value="2">Sesi 2</option>
                    <option value="3">Sesi 3</option>
                </select>
                <button id="theme-toggle" type="button"
                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-hidden whitespace-nowrap">
                <p class="marquee-text text-2xl font-bold text-gray-800 dark:text-gray-200">Antrian Pendaftaran RSU
                    Avisena</p>
            </div>
            <div class="flex-1 flex justify-end items-center gap-6">
                <div id="clock" class="text-2xl font-bold text-gray-800 dark:text-gray-200"></div>
                <a href="../index.php"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </header>
        <main class="flex-grow">
            <div id="dokter-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sesiSelect = document.getElementById('sesi-select');
            const dokterGrid = document.getElementById('dokter-grid');
            const clockElement = document.getElementById('clock');
            const apiUrl = '../api/dokter_poli.php';
            let synth; // Deklarasikan synth di scope yang lebih luas

            // Inisialisasi Tone.js setelah interaksi pengguna pertama
            function initAudio() {
                if (!synth) {
                    synth = new Tone.Synth().toDestination();
                    console.log("Audio context dimulai.");
                }
            }
            document.body.addEventListener('click', initAudio, { once: true });

            // Fungsi untuk memainkan suara notifikasi
            function playNotificationSound() {
                if (synth) {
                    // Mainkan nada C5 selama 0.2 detik, lalu G5 selama 0.3 detik
                    synth.triggerAttackRelease("C5", "8n", Tone.now());
                    synth.triggerAttackRelease("G5", "8n", Tone.now() + 0.2);
                }
            }

            function createDokterCard(dokter) {
                // Logika Status Dokter
                let statusBadge = ''; // Defaultnya kosong
                const now = new Date();
                const currentTime = now.getHours() * 60 + now.getMinutes();

                if (dokter.Jam_Praktek) {
                    const parts = dokter.Jam_Praktek.split('-').map(t => t.trim());
                    if (parts.length === 2) {
                        const [startHour, startMin] = parts[0].split(':').map(Number);
                        const [endHour, endMin] = parts[1].split(':').map(Number);
                        const startTime = startHour * 60 + startMin;
                        const endTime = endHour * 60 + endMin;

                        if (currentTime >= startTime && currentTime <= endTime) {
                            // Hanya buat badge jika statusnya 'Praktek'
                            statusBadge = `<span class="absolute top-2 right-2 text-xs font-bold text-white bg-green-500 px-2 py-1 rounded-full">Praktek</span>`;
                        }
                    }
                }

                return `
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden flex flex-col" data-id="${dokter.Nama_Dr}">
                    <div class="bg-blue-600 dark:bg-gray-700 p-5 text-center relative">
                        <h3 class="text-3xl font-bold text-white truncate">${dokter.Nama_Dr}</h3>
                        <p class="text-lg text-blue-200 dark:text-gray-300">Poli ${dokter.Poli || 'Poli Umum'}</p>
                        ${statusBadge}
                    </div>
                    <div class="p-6 flex-grow flex flex-col justify-center">
                        <div class="text-center mb-4">
                            <p class="text-2xl text-gray-500 dark:text-gray-400 mb-1">Nomor Antrian</p>
                            <p class="text-8xl font-black text-blue-600 dark:text-yellow-400 transition-all duration-300" data-field="no-antrian">${dokter.NoAntri || '-'}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl text-gray-500 dark:text-gray-400 mb-1">Loket</p>
                            <p class="text-5xl font-bold text-gray-900 dark:text-white transition-all duration-300" data-field="loket">${dokter.Loket || '-'}</p>
                        </div>
                    </div>
                </div>
            `;
            }

            function updateDokterGrid(newData) {
                const existingCards = new Map();
                let soundPlayed = false;
                dokterGrid.querySelectorAll('[data-id]').forEach(card => {
                    existingCards.set(card.dataset.id, card);
                });

                newData.forEach(dokter => {
                    const cardId = dokter.Nama_Dr;
                    const existingCard = existingCards.get(cardId);

                    if (existingCard) {
                        const noAntrianEl = existingCard.querySelector('[data-field="no-antrian"]');
                        const loketEl = existingCard.querySelector('[data-field="loket"]');
                        const currentAntrian = noAntrianEl.textContent;
                        const newAntrian = (dokter.NoAntri || '-').toString();

                        if (currentAntrian !== newAntrian) {
                            noAntrianEl.textContent = newAntrian;
                            noAntrianEl.classList.add('value-update');
                            setTimeout(() => noAntrianEl.classList.remove('value-update'), 1500);
                            if (!soundPlayed) { // Hanya mainkan suara sekali per pembaruan
                                playNotificationSound();
                                soundPlayed = true;
                            }
                        }
                        if (loketEl.textContent !== (dokter.Loket || '-').toString()) {
                            loketEl.textContent = dokter.Loket || '-';
                        }
                        existingCards.delete(cardId);
                    } else {
                        const cardHtml = createDokterCard(dokter);
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = cardHtml;
                        dokterGrid.appendChild(tempDiv.firstElementChild);
                    }
                });

                existingCards.forEach(cardToRemove => cardToRemove.remove());
                if (dokterGrid.innerHTML === '') {
                    dokterGrid.innerHTML = '<p class="text-center col-span-full text-xl text-gray-500">Tidak ada dokter yang praktek di sesi ini.</p>';
                }
            }

            async function fetchAndDisplayDokter() {
                const sesi = sesiSelect.value;
                try {
                    const response = await fetch(`${apiUrl}?sesi=${sesi}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        updateDokterGrid(result.data);
                    } else {
                        dokterGrid.innerHTML = '<p class="text-center col-span-full text-xl text-gray-500">Tidak ada dokter yang praktek di sesi ini.</p>';
                    }
                } catch (error) {
                    console.error('Gagal mengambil data dokter:', error);
                    dokterGrid.innerHTML = '<p class="text-center col-span-full text-xl text-red-500">Gagal memuat data.</p>';
                }
            }

            sesiSelect.addEventListener('change', fetchAndDisplayDokter);

            function connectWebSocket() {
                const wsHost = `ws://${window.location.hostname}:8080`;
                const socket = new WebSocket(wsHost);
                socket.onopen = () => console.log("Koneksi WebSocket Display berhasil dibuat!");
                socket.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    if (data.event === 'data_updated') {
                        fetchAndDisplayDokter();
                    }
                };
                socket.onclose = () => setTimeout(connectWebSocket, 2000);
                socket.onerror = (error) => console.error(`Error WebSocket: ${error.message}`);
            }

            // Fungsi Jam Digital
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }

            setInterval(updateClock, 1000);
            updateClock();
            fetchAndDisplayDokter();
            connectWebSocket();
        });

        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleButton = document.getElementById('theme-toggle');

        function setToggleIcon() {
            if (localStorage.getItem('color-theme') === 'dark') {
                themeToggleDarkIcon.classList.add('hidden');
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
                themeToggleLightIcon.classList.add('hidden');
            }
        }
        setToggleIcon();
        themeToggleButton.addEventListener('click', function () {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('color-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            setToggleIcon();
        });
    </script>

</body>

</html>