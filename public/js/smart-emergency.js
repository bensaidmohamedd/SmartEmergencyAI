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
        return resolveAddress(lat, lng).then(function () {
            if (typeof window.triggerAiAnalysis === 'function') window.triggerAiAnalysis();
        });
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

    /* ======================================================================
       FORMULAIRE INCENDIE + ANALYSE IA
       ====================================================================== */
    var reportCategory = document.getElementById('reportCategory');
    var fireFields = document.getElementById('fireFields');
    var reportDescription = document.getElementById('reportDescription');
    var aiPanel = document.getElementById('aiPreviewPanel');
    var aiPreviewCard = document.getElementById('aiPreviewCard');
    var reportSubmitBtn = document.getElementById('reportSubmitBtn');
    var analyzeTimer = null;

    function toggleFireFields() {
        if (!fireFields || !reportCategory) return;
        fireFields.classList.toggle('d-none', reportCategory.value !== 'Incendie');
    }

    if (reportCategory) {
        reportCategory.addEventListener('change', function () {
            toggleFireFields();
            triggerAnalysis();
        });
        toggleFireFields();
    }

    function applyVerdictUi(data) {
        var banner = document.getElementById('aiVerdictBanner');
        var title = document.getElementById('aiVerdictTitle');
        var list = document.getElementById('aiRejectionList');
        var badge = document.getElementById('aiVerdictBadge');
        var pulse = document.getElementById('aiPulseDot');

        if (!banner || !title) return;

        banner.classList.remove('d-none', 'ai-verdict-approved', 'ai-verdict-review', 'ai-verdict-rejected');
        if (aiPreviewCard) {
            aiPreviewCard.classList.remove('ai-card-approved', 'ai-card-review', 'ai-card-rejected');
        }

        if (data.verdict === 'rejected') {
            banner.classList.add('ai-verdict-rejected');
            if (aiPreviewCard) aiPreviewCard.classList.add('ai-card-rejected');
            title.innerHTML = '<i class="bi bi-x-octagon-fill me-1"></i> Signalement rejeté — fausse urgence détectée';
            if (list) {
                list.classList.remove('d-none');
                list.innerHTML = '';
                (data.rejection_reasons || []).forEach(function (r) {
                    var li = document.createElement('li');
                    li.textContent = r;
                    list.appendChild(li);
                });
            }
            if (badge) { badge.textContent = 'REJETÉ'; badge.className = 'badge bg-danger ms-1'; badge.classList.remove('d-none'); }
            if (pulse) pulse.style.background = '#e74c3c';
            if (reportSubmitBtn) { reportSubmitBtn.disabled = true; reportSubmitBtn.innerHTML = '<i class="bi bi-shield-x me-2"></i> Signalement bloqué par l\'IA'; }
        } else if (data.verdict === 'review') {
            banner.classList.add('ai-verdict-review');
            if (aiPreviewCard) aiPreviewCard.classList.add('ai-card-review');
            title.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Signalement suspect — vérification humaine requise';
            if (list) list.classList.add('d-none');
            if (badge) { badge.textContent = 'À VÉRIFIER'; badge.className = 'badge bg-warning text-dark ms-1'; badge.classList.remove('d-none'); }
            if (pulse) pulse.style.background = '#f39c12';
            if (reportSubmitBtn) { reportSubmitBtn.disabled = false; reportSubmitBtn.innerHTML = '<i class="bi bi-send-fill me-2"></i> Signaler (sous vérification)'; }
        } else {
            banner.classList.add('ai-verdict-approved');
            if (aiPreviewCard) aiPreviewCard.classList.add('ai-card-approved');
            title.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Signalement validé par l\'IA';
            if (list) list.classList.add('d-none');
            if (badge) { badge.textContent = 'VALIDÉ'; badge.className = 'badge bg-success ms-1'; badge.classList.remove('d-none'); }
            if (pulse) pulse.style.background = '#27ae60';
            if (reportSubmitBtn) { reportSubmitBtn.disabled = false; reportSubmitBtn.innerHTML = '<i class="bi bi-send-fill me-2"></i> Signaler l\'urgence'; }
        }
    }

    function triggerAnalysis() {
        if (!reportDescription || !window.SEA_ANALYZE_URL) return;
        var desc = reportDescription.value.trim();
        var cat = reportCategory ? reportCategory.value : '';
        if (desc.length < 10 || !cat) {
            if (aiPanel) aiPanel.classList.add('d-none');
            if (reportSubmitBtn) {
                reportSubmitBtn.disabled = false;
                reportSubmitBtn.innerHTML = '<i class="bi bi-send-fill me-2"></i> Signaler l\'urgence';
            }
            return;
        }
        clearTimeout(analyzeTimer);
        analyzeTimer = setTimeout(function () {
            var hasMedia = (reportPhoto && reportPhoto.files && reportPhoto.files.length > 0)
                || (reportVideo && reportVideo.files && reportVideo.files.length > 0);
            var body = {
                category: cat,
                description: desc,
                latitude: latInput && latInput.value ? parseFloat(latInput.value) : null,
                longitude: lngInput && lngInput.value ? parseFloat(lngInput.value) : null,
                has_media: hasMedia,
                fire_people_trapped: document.getElementById('firePeopleTrapped')?.value === '1',
                fire_smoke_level: document.getElementById('fireSmokeLevel')?.value || null,
                fire_building_type: document.getElementById('fireBuildingType')?.value || null,
            };
            fetch(window.SEA_ANALYZE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.SEA_CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!aiPanel) return;
                aiPanel.classList.remove('d-none');
                document.getElementById('aiScoreValue').textContent = data.priority_rank ?? data.score;
                document.getElementById('aiScoreRing').style.setProperty('--ai-score', data.priority_rank ?? data.score);
                document.getElementById('aiCredibilityValue').textContent = data.credibility_score ?? '—';
                document.getElementById('aiCredibilityRing').style.setProperty('--ai-score', data.credibility_score ?? 0);
                var gravBadge = document.getElementById('aiGraviteBadge');
                gravBadge.textContent = data.gravite.toUpperCase();
                gravBadge.className = 'badge gravite-' + data.gravite;
                document.getElementById('aiPriorityLabel').textContent = data.priority_label || '';
                document.getElementById('aiSummaryText').textContent = data.summary;
                document.getElementById('aiEtaText').textContent = data.can_submit
                    ? 'Temps estimé : ~' + data.estimated_response_min + ' min'
                    : 'Envoi bloqué — corrigez les alertes ci-dessus.';
                var svcList = document.getElementById('aiServicesList');
                svcList.innerHTML = '';
                (data.services || []).forEach(function (s) {
                    var span = document.createElement('span');
                    span.className = 'badge bg-danger-subtle text-danger';
                    span.textContent = s;
                    svcList.appendChild(span);
                });
                applyVerdictUi(data);
            })
            .catch(function () {});
        }, 600);
    }

    window.triggerAiAnalysis = triggerAnalysis;

    if (reportDescription) {
        reportDescription.addEventListener('input', triggerAnalysis);
    }
    ['firePeopleTrapped', 'fireSmokeLevel', 'fireBuildingType'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', triggerAnalysis);
    });
    if (reportPhoto) reportPhoto.addEventListener('change', triggerAnalysis);
    if (reportVideo) reportVideo.addEventListener('change', triggerAnalysis);

});
