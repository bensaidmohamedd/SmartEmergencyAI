{{-- Badge statut : en_cours, termine --}}
@php
    $statutLabels = [
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
    ];
@endphp
<span class="badge-statut statut-{{ $statut }}">{{ $statutLabels[$statut] ?? $statut }}</span>
