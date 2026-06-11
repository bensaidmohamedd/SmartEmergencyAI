/**
 * Smart Emergency AI — MVP Citoyen
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ======================================================================
       DARK / LIGHT MODE
       ====================================================================== */
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    function setTheme(theme) {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('sea-theme', theme);
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var current = html.getAttribute('data-theme') || 'light';
            setTheme(current === 'light' ? 'dark' : 'light');
        });
    }


    /* ======================================================================
       SIDEBAR MOBILE
       ====================================================================== */
    var sidebar = document.getElementById('appSidebar');
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebarOverlay = document.getElementById('sidebarOverlay');
    var sidebarClose = document.getElementById('sidebarClose');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) closeSidebar();
    });


    /* ======================================================================
       FILTRAGE HISTORIQUE
       ====================================================================== */
    var historySearch = document.getElementById('historySearch');
    var historyGravite = document.getElementById('historyGravite');
    var historyStatut = document.getElementById('historyStatut');
    var historyGrid = document.getElementById('historyGrid');
    var historyCount = document.getElementById('historyCount');
    var historyEmpty = document.getElementById('historyEmpty');

    function filterHistory() {
        if (!historyGrid) return;

        var search = historySearch ? historySearch.value.toLowerCase().trim() : '';
        var gravite = historyGravite ? historyGravite.value : 'all';
        var statut = historyStatut ? historyStatut.value : 'all';
        var cards = historyGrid.querySelectorAll('.history-card-wrapper');
        var visible = 0;

        cards.forEach(function (card) {
            var matchGravite = gravite === 'all' || card.dataset.gravite === gravite;
            var matchStatut = statut === 'all' || card.dataset.statut === statut;
            var matchSearch = search === '' || card.textContent.toLowerCase().includes(search);

            if (matchGravite && matchStatut && matchSearch) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        if (historyCount) historyCount.textContent = visible + ' résultat(s)';
        if (historyEmpty) historyEmpty.classList.toggle('d-none', visible > 0);
    }

    if (historySearch) historySearch.addEventListener('input', filterHistory);
    if (historyGravite) historyGravite.addEventListener('change', filterHistory);
    if (historyStatut) historyStatut.addEventListener('change', filterHistory);


    /* ======================================================================
       APERÇU PHOTO / VIDÉO
       ====================================================================== */
    var reportPhoto = document.getElementById('reportPhoto');
    var photoPreview = document.getElementById('photoPreview');

    if (reportPhoto && photoPreview) {
        reportPhoto.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu" class="img-fluid rounded">';
                    photoPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    var reportVideo = document.getElementById('reportVideo');
    var videoPreview = document.getElementById('videoPreview');

    if (reportVideo && videoPreview) {
        reportVideo.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                videoPreview.innerHTML = '<span class="badge bg-primary"><i class="bi bi-camera-video me-1"></i>' + this.files[0].name + '</span>';
                videoPreview.classList.remove('d-none');
            }
        });
    }


    /* ======================================================================
       GÉOLOCALISATION
       ====================================================================== */
    var geoBtn = document.getElementById('geoBtn');
    var geoCard = document.getElementById('geoCard');
    var geoStatus = document.getElementById('geoStatus');
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var localisationInput = document.getElementById('localisation');
    var geoCoords = document.getElementById('geoCoords');
    var geoMap = document.getElementById('geoMap');

    function setGeoStatus(message, type) {
        if (!geoStatus) return;
        geoStatus.className = 'geo-status small ' + (type === 'ok' ? 'geo-ok' : 'text-muted');
        geoStatus.innerHTML = message;
    }

    function showMap(lat, lng) {
        if (!geoMap) return;
        geoMap.classList.remove('d-none');
        geoMap.innerHTML = '<iframe src="https://maps.google.com/maps?q=' + lat + ',' + lng + '&z=16&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    }

    function resolveAddress(lat, lng) {
        var url = '/geolocalisation/adresse?latitude=' + encodeURIComponent(lat) + '&longitude=' + encodeURIComponent(lng);

        return fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (localisationInput && data.address) {
                    localisationInput.value = data.address;
                }
            })
            .catch(function () {
                if (localisationInput) {
                    localisationInput.value = 'Position GPS (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ')';
                }
            });
    }

    function applyPosition(lat, lng) {
        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;

        if (geoCoords) {
            geoCoords.textContent = 'Coordonnées : ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
            geoCoords.classList.remove('d-none');
        }

        if (geoCard) {
            geoCard.classList.remove('geo-error');
            geoCard.classList.add('geo-success');
        }

        setGeoStatus('<i class="bi bi-check-circle-fill me-1"></i> Position obtenue avec succès', 'ok');
        showMap(lat, lng);
        return resolveAddress(lat, lng);
    }

    if (geoBtn) {
        geoBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                setGeoStatus('<i class="bi bi-x-circle me-1"></i> Géolocalisation non supportée par votre navigateur', 'error');
                if (geoCard) geoCard.classList.add('geo-error');
                return;
            }

            setGeoStatus('<i class="bi bi-hourglass-split me-1"></i> Recherche de votre position...', 'loading');
            geoBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    applyPosition(position.coords.latitude, position.coords.longitude)
                        .finally(function () { geoBtn.disabled = false; });
                },
                function (error) {
                    geoBtn.disabled = false;
                    if (geoCard) geoCard.classList.add('geo-error');

                    var msg = 'Impossible d\'obtenir votre position.';
                    if (error.code === 1) msg = 'Autorisez l\'accès à votre position dans le navigateur.';
                    if (error.code === 2) msg = 'Position indisponible. Réessayez.';
                    if (error.code === 3) msg = 'Délai dépassé. Réessayez.';

                    setGeoStatus('<i class="bi bi-x-circle me-1"></i> ' + msg, 'error');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });

        // Restaurer la position après erreur de validation
        if (latInput && lngInput && latInput.value && lngInput.value) {
            var lat = parseFloat(latInput.value);
            var lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                applyPosition(lat, lng);
            }
        }
    }

});
