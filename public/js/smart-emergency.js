/**
 * Smart Emergency AI — MVP Citoyen
 * Thème dark/light, sidebar, filtres, formulaires
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
       FORMULAIRE SIGNALEMENT
       ====================================================================== */
    var reportForm = document.getElementById('reportForm');
    var reportModal = document.getElementById('reportModal');
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

    if (reportForm && reportModal) {
        var modal = new bootstrap.Modal(reportModal);
        reportForm.addEventListener('submit', function (e) {
            e.preventDefault();
            modal.show();
        });
    }


    /* ======================================================================
       FORMULAIRES AUTH (redirection simulée)
       ====================================================================== */
    var loginForm = document.getElementById('loginForm');
    var registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            window.location.href = loginForm.getAttribute('action') || '/dashboard';
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var pwd = document.getElementById('password');
            var confirm = document.getElementById('passwordConfirm');
            if (pwd && confirm && pwd.value !== confirm.value) {
                alert('Les mots de passe ne correspondent pas.');
                return;
            }
            window.location.href = registerForm.getAttribute('action') || '/dashboard';
        });
    }

});
