<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation {{ $signalement->reference }} — Smart Emergency AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 2rem; }
        .attestation-header { border-bottom: 3px solid #1877F2; padding-bottom: 1rem; margin-bottom: 2rem; }
        .attestation-seal { width: 80px; height: 80px; background: #1877F2; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
        @media print { .no-print { display: none !important; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i> Imprimer / PDF</button>
        <button onclick="window.close()" class="btn btn-outline-secondary ms-2">Fermer</button>
    </div>

    <div class="attestation-header d-flex justify-content-between align-items-start">
        <div>
            <h2 class="fw-bold mb-1">Smart Emergency AI — Niger</h2>
            <p class="text-muted mb-0">Attestation officielle de signalement d'urgence</p>
        </div>
        <div class="attestation-seal"><i class="bi bi-shield-check"></i></div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Référence</h6>
            <p class="fs-4 fw-bold">{{ $signalement->reference }}</p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Date et heure</h6>
            <p class="fw-semibold">{{ $signalement->reported_at->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    <table class="table table-bordered">
        <tr><th width="35%">Citoyen</th><td>{{ $signalement->user->name }} — {{ $signalement->user->phone }}</td></tr>
        <tr><th>Catégorie</th><td>{{ $signalement->category->name }}</td></tr>
        <tr><th>Gravité / Priorité</th><td>{{ strtoupper($signalement->gravite) }} @if($signalement->ai_score) — Score IA {{ $signalement->ai_score }}/100 @endif</td></tr>
        <tr><th>Localisation</th><td>{{ $signalement->localisation }} @if($signalement->latitude)({{ $signalement->latitude }}, {{ $signalement->longitude }})@endif</td></tr>
        <tr><th>Description</th><td>{{ $signalement->description }}</td></tr>
        @if($signalement->ai_summary)<tr><th>Analyse IA</th><td>{{ $signalement->ai_summary }}</td></tr>@endif
        @if($signalement->assignedService)<tr><th>Unité dispatchée</th><td>{{ $signalement->assignedService->name }} ({{ $signalement->assignedService->phone }})</td></tr>@endif
        <tr><th>Statut</th><td>{{ $signalement->statut === 'en_cours' ? 'En cours d\'intervention' : ucfirst($signalement->statut) }}</td></tr>
    </table>

    <p class="small text-muted mt-4">
        Ce document certifie que le signalement ci-dessus a été enregistré sur la plateforme Smart Emergency AI
        et transmis aux services compétents. Document généré le {{ now()->format('d/m/Y à H:i') }}.
    </p>

    <div class="mt-5 pt-4 border-top">
        <div class="row">
            <div class="col-6"><small class="text-muted">Signature numérique</small><br><code>{{ md5($signalement->reference.$signalement->created_at) }}</code></div>
            <div class="col-6 text-end"><small class="text-muted">Smart Emergency AI — Niamey, Niger</small></div>
        </div>
    </div>
</body>
</html>
