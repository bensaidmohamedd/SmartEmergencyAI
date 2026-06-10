{{-- Footer moderne — Smart Emergency AI Niger --}}
<footer class="sea-footer-modern" id="contact">
    <div class="footer-top">
        <div class="container">
            <div class="row g-5">
                {{-- Marque --}}
                <div class="col-lg-4">
                    <a href="{{ route('home') }}" class="footer-brand d-flex align-items-center gap-2 text-decoration-none mb-3">
                        <span class="brand-logo"><i class="bi bi-shield-exclamation"></i></span>
                        <span class="brand-text fs-5">Smart Emergency AI</span>
                    </a>
                    <p class="footer-desc">
                        La plateforme nigérienne qui connecte les citoyens aux services de secours
                        à Niamey et dans tout le Niger, en quelques secondes grâce à l'intelligence artificielle.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="col-6 col-lg-2">
                    <h6 class="footer-heading">Navigation</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Accueil</a></li>
                        <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                        <li><a href="#comment-ca-marche">Comment ça marche</a></li>
                        <li><a href="{{ route('report') }}">Signaler</a></li>
                    </ul>
                </div>

                {{-- Compte --}}
                <div class="col-6 col-lg-2">
                    <h6 class="footer-heading">Compte</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Connexion</a></li>
                        <li><a href="{{ route('register') }}">Inscription</a></li>
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('history') }}">Historique</a></li>
                    </ul>
                </div>

                {{-- Contact Niger --}}
                <div class="col-lg-4">
                    <h6 class="footer-heading">Contact</h6>
                    <ul class="footer-contact">
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <span>contact@smartemergency.ne</span>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <span>+227 87 14 51 44</span>
                        </li>
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Plateau, Niamey — Niger</span>
                        </li>
                        <li>
                            <i class="bi bi-headset"></i>
                            <span>Support 24h/24 — 7j/7</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <small class="footer-copy">
                    <i class="bi bi-c-circle"></i>
                    {{ date('Y') }} Smart Emergency AI Niger. Tous droits réservés.
                </small>
                <div class="footer-legal">
                    <a href="#">Mentions légales</a>
                    <a href="#">Confidentialité</a>
                </div>
            </div>
        </div>
    </div>
</footer>
