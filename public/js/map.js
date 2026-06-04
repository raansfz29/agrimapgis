document.addEventListener("DOMContentLoaded", () => {
    // 1. Initialize Map (Coordinate focused on Indonesia roughly)
    const map = L.map('map', {
        zoomControl: false // We'll move it to top right later if needed
    }).setView([-2.5489, 118.0149], 5);

    // Reposition Zoom Control
    L.control.zoom({
        position: 'topright'
    }).addTo(map);

    // 2. Base Maps
    const osmBase = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    });

    const googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google'
    });

    // Add OpenStreetMap as default
    osmBase.addTo(map);

    // Layer Control
    const baseMaps = {
        "OpenStreetMap": osmBase,
        "Google Satellite": googleSat
    };
    L.control.layers(baseMaps, null, { position: 'bottomright' }).addTo(map);

    // 3. Feature Groups for Lands
    const landLayerGroup = L.featureGroup().addTo(map);

    // 4. Initialize Leaflet.draw
    const drawControl = new L.Control.Draw({
        edit: {
            featureGroup: landLayerGroup
        },
        draw: {
            polygon: {
                allowIntersection: false, // Restricts shapes to simple polygons
                drawError: {
                    color: '#e1e100', // Color the shape will turn when intersects
                    message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                },
                shapeOptions: {
                    color: '#22c55e'
                }
            },
            // Disable other drawing tools for now
            polyline: false,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        }
    });
    // Don't add to map visually, we'll trigger it programmatically
    // map.addControl(drawControl); 

    // Instantiate a draw polygon handler
    const polygonDrawer = new L.Draw.Polygon(map, drawControl.options.draw.polygon);

    // 5. UI Events
    document.getElementById('btnLocateMe').addEventListener('click', () => {
        if (navigator.geolocation) {
            map.locate({setView: true, maxZoom: 16});
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    });

    map.on('locationfound', (e) => {
        L.marker(e.latlng).addTo(map)
            .bindPopup("Lokasi Anda saat ini").openPopup();
    });

    map.on('locationerror', (e) => {
        alert("Gagal menemukan lokasi: " + e.message);
    });

    // Custom Button to trigger Draw Polygon
    document.getElementById('btnDrawPolygon').addEventListener('click', () => {
        polygonDrawer.enable();
    });

    // 6. Handle Draw Events
    let tempDrawnLayer = null;
    const modalSaveLand = new bootstrap.Modal(document.getElementById('modalSaveLand'));

    map.on(L.Draw.Event.CREATED, function (e) {
        const layer = e.layer;
        tempDrawnLayer = layer;
        
        // Convert drawn layer to GeoJSON
        const geojson = layer.toGeoJSON();
        document.getElementById('geojson_data').value = JSON.stringify(geojson.geometry);

        // Show Modal
        modalSaveLand.show();
    });

    // Handle Cancel Draw in Modal
    document.getElementById('btnCancelDraw').addEventListener('click', () => {
        tempDrawnLayer = null;
    });

    // Handle Submit Land
    document.getElementById('btnSubmitLand').addEventListener('click', () => {
        const namaLahan = document.getElementById('nama_lahan').value;
        const komoditas = document.getElementById('komoditas').value;
        const geojsonData = document.getElementById('geojson_data').value;

        if (!namaLahan) {
            alert("Nama lahan harus diisi!");
            return;
        }

        const payload = {
            nama_lahan: namaLahan,
            komoditas: komoditas,
            geojson: geojsonData
        };

        // Disable button during submit
        const btnSubmit = document.getElementById('btnSubmitLand');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Menyimpan...';

        fetch('/api/lands/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Simpan Lahan';

            if (data.status === 'success') {
                // Add layer to map permanently
                if (tempDrawnLayer) {
                    tempDrawnLayer.bindPopup(`<b>${namaLahan}</b><br>Komoditas: ${komoditas}`);
                    landLayerGroup.addLayer(tempDrawnLayer);
                }
                modalSaveLand.hide();
                document.getElementById('formSaveLand').reset();
                alert("Lahan berhasil disimpan!");
            } else {
                alert("Gagal menyimpan lahan: " + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Simpan Lahan';
            alert("Terjadi kesalahan jaringan.");
        });
    });

    // Filtering logic stub
    document.getElementById('filterKomoditas').addEventListener('change', (e) => {
        console.log("Filter Komoditas: ", e.target.value);
    });

    document.getElementById('filterFase').addEventListener('change', (e) => {
        console.log("Filter Fase: ", e.target.value);
    });

    // 7. Load Lands from Database
    function loadLands() {
        fetch('/map/api-lands')
            .then(response => response.json())
            .then(data => {
                if (data.type === 'FeatureCollection') {
                    L.geoJSON(data, {
                        style: function (feature) {
                            // Style based on komoditas
                            let color = '#22c55e'; // Green for padi
                            if (feature.properties.komoditas === 'jagung') {
                                color = '#eab308'; // Yellow for jagung
                            }
                            return { color: color, weight: 2, fillOpacity: 0.4 };
                        },
                        onEachFeature: function (feature, layer) {
                            const props = feature.properties;
                            layer.bindPopup(`<b>${props.nama_lahan}</b><br>Komoditas: ${props.komoditas}<br>Fase: ${props.status_fase}`);
                            landLayerGroup.addLayer(layer);
                        }
                    });
                }
            })
            .catch(error => console.error('Error loading lands:', error));
    }

    // Call loadLands on startup
    loadLands();
});
