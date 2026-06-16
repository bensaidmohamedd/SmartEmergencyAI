<div class="ai-preview-card p-4" id="aiPreviewCard">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="ai-pulse-dot" id="aiPulseDot"></span>
        <h6 class="fw-semibold mb-0"><i class="bi bi-robot me-2"></i>Analyse IA en temps réel</h6>
    </div>
    <div class="ai-verdict-banner d-none mb-3" id="aiVerdictBanner">
        <div class="fw-semibold" id="aiVerdictTitle"></div>
        <ul class="small mb-0 mt-1 ps-3 d-none" id="aiRejectionList"></ul>
    </div>
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <div class="ai-score-ring" id="aiScoreRing"><span id="aiScoreValue">—</span></div>
            <div class="text-center small text-muted mt-1">Priorité</div>
        </div>
        <div class="col-auto">
            <div class="ai-score-ring ai-credibility-ring" id="aiCredibilityRing"><span id="aiCredibilityValue">—</span></div>
            <div class="text-center small text-muted mt-1">Crédibilité</div>
        </div>
        <div class="col">
            <div class="mb-1">
                <span class="badge" id="aiGraviteBadge">—</span>
                <span class="badge ms-1 d-none" id="aiVerdictBadge"></span>
                <span class="small text-muted ms-1" id="aiPriorityLabel"></span>
            </div>
            <p class="small mb-2" id="aiSummaryText">Saisissez une description pour lancer l'analyse...</p>
            <div id="aiServicesList" class="d-flex flex-wrap gap-1"></div>
            <small class="text-muted" id="aiEtaText"></small>
        </div>
    </div>
</div>
