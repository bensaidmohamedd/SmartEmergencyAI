{{-- Badge gravité : critique, elevee, moyenne, faible --}}
@php
    $graviteLabels = [
        'critique' => 'Critique',
        'elevee' => 'Élevée',
        'moyenne' => 'Moyenne',
        'faible' => 'Faible',
    ];
@endphp
<span class="badge-gravite gravite-{{ $gravite }}">{{ $graviteLabels[$gravite] ?? $gravite }}</span>
