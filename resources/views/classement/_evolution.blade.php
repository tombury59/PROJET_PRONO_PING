@php
    $dates = $evolution['dates'];
    $series = $evolution['series'];
    $moiId = auth()->id();

    // Top 6 par total, en s'assurant que le joueur courant est présent.
    $affiches = $series->take(6);
    if (! $affiches->contains(fn ($s) => $s['user']->id === $moiId)) {
        $moi = $series->firstWhere('user.id', $moiId);
        if ($moi) {
            $affiches = $affiches->push($moi);
        }
    }

    $n = count($dates);
    $max = max(1, (int) $series->max('final'));

    $W = 720; $H = 300; $pl = 32; $pr = 12; $pt = 12; $pb = 26;
    $plotW = $W - $pl - $pr; $plotH = $H - $pt - $pb;

    $x = fn ($i) => $n <= 1 ? $pl + $plotW / 2 : $pl + $plotW * $i / ($n - 1);
    $y = fn ($v) => $pt + $plotH * (1 - $v / $max);

    $palette = ['#059669', '#d97706', '#dc2626', '#0891b2', '#7c3aed', '#db2777', '#65a30d'];
@endphp

<x-card class="p-4">
    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-surface-500 dark:text-surface-400">
        Évolution des points (rencontres)
    </h3>

    <div class="overflow-x-auto">
        <svg viewBox="0 0 {{ $W }} {{ $H }}" class="h-64 w-full min-w-[520px]" role="img" aria-label="Évolution des points">
            {{-- Grille horizontale + graduations Y --}}
            @foreach ([0, 0.5, 1] as $t)
                @php($gy = $pt + $plotH * (1 - $t))
                <line x1="{{ $pl }}" y1="{{ $gy }}" x2="{{ $W - $pr }}" y2="{{ $gy }}" stroke="currentColor" class="text-surface-200 dark:text-surface-700" stroke-width="1" />
                <text x="{{ $pl - 6 }}" y="{{ $gy + 3 }}" text-anchor="end" class="fill-surface-400" font-size="10">{{ round($max * $t) }}</text>
            @endforeach

            {{-- Étiquettes X (première, milieu, dernière date) --}}
            @foreach ([0, intdiv($n - 1, 2), $n - 1] as $i)
                @if ($i >= 0 && $i < $n)
                    <text x="{{ $x($i) }}" y="{{ $H - 8 }}" text-anchor="middle" class="fill-surface-400" font-size="10">
                        {{ \Illuminate\Support\Carbon::parse($dates[$i])->format('d/m') }}
                    </text>
                @endif
            @endforeach

            {{-- Courbes : les autres d'abord, le joueur courant par-dessus --}}
            @foreach ($affiches as $index => $s)
                @php($estMoi = $s['user']->id === $moiId)
                @php($couleur = $estMoi ? '#4f46e5' : $palette[$index % count($palette)])

                @if ($n === 1)
                    <circle cx="{{ $x(0) }}" cy="{{ $y($s['cumul'][0]) }}" r="{{ $estMoi ? 4 : 3 }}" fill="{{ $couleur }}" />
                @else
                    <polyline
                        fill="none"
                        stroke="{{ $couleur }}"
                        stroke-width="{{ $estMoi ? 2.5 : 1.3 }}"
                        stroke-linejoin="round"
                        stroke-linecap="round"
                        opacity="{{ $estMoi ? 1 : 0.7 }}"
                        points="@foreach ($s['cumul'] as $i => $v){{ $x($i) }},{{ $y($v) }} @endforeach"
                    />
                @endif
            @endforeach
        </svg>
    </div>

    {{-- Légende --}}
    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1">
        @foreach ($affiches as $index => $s)
            @php($estMoi = $s['user']->id === $moiId)
            @php($couleur = $estMoi ? '#4f46e5' : $palette[$index % count($palette)])
            <span class="inline-flex items-center gap-1.5 text-xs {{ $estMoi ? 'font-semibold text-surface-900 dark:text-white' : 'text-surface-600 dark:text-surface-400' }}">
                <span class="inline-block size-2.5 rounded-full" style="background-color: {{ $couleur }}"></span>
                {{ $s['user']->pseudo }}{{ $estMoi ? ' (toi)' : '' }} · {{ $s['final'] }}
            </span>
        @endforeach
    </div>
</x-card>
