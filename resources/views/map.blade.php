@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Peta Leaflet - Simpan & Hapus Titik</h1>
    <p>Klik pada peta untuk menambahkan marker baru. Klik marker untuk menghapusnya.</p>

    <div id="map" style="height: 500px; border: 1px solid #ccc; border-radius: 8px;"></div>
</div>

<!-- Token CSRF untuk AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const map = L.map('map').setView([-6.2, 106.816666], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Ambil semua marker dari database
        fetch("/markers")
            .then(response => response.json())
            .then(data => {
                console.log("DATA MARKERS DARI BACKEND:", data); // 👈 ini penting
                if (Array.isArray(data)) {
                    data.forEach(marker => {
                        console.log("Render marker:", marker); // 👈 debug isi marker
                        const leafletMarker = L.marker([marker.lat, marker.lng]).addTo(map);
                        leafletMarker.bindPopup(`
                            <div>
                                <p><strong>Lat:</strong> ${(Number(marker.lat)).toFixed(6)}<br>
                                   <strong>Lng:</strong> ${(Number(marker.lng)).toFixed(6)}</p>
                                <button onclick="deleteMarker(${marker.id})">🗑 Hapus</button>
                            </div>
                        `);
                    });
                } else {
                    console.warn("Data marker tidak valid:", data);
                }
            });

        // Tambah marker baru saat klik di peta
        map.on('click', function (e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Tambahkan marker ke peta secara instan
            const newMarker = L.marker([lat, lng]).addTo(map);
            newMarker.bindPopup(`<p>Marker baru ditambahkan.</p>`).openPopup();

            // Kirim ke server via AJAX
            fetch("/markers", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ lat, lng })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Marker disimpan:", data);
            })
            .catch(error => {
                console.error("Gagal menyimpan marker:", error);
            });
        });
    });

    // Fungsi hapus marker
    function deleteMarker(id) {
        fetch(`/markers/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
            location.reload();
        })
        .catch(error => {
            console.error("Gagal menghapus marker:", error);
        });
    }
</script>
@endsection
