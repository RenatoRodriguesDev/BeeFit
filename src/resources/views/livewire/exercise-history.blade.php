<div class="space-y-4 mt-4">

    @if($history->isEmpty())
        <p class="text-zinc-400 text-sm">{{ __('app.no_history_yet') }}</p>
    @else

        {{-- Gráfico de evolução --}}
        <div class="relative" style="height:160px">
            <canvas id="historyChart-{{ $exerciseId }}"></canvas>
        </div>

        <div class="flex items-center gap-4 text-xs text-zinc-400 mb-2">
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-5 h-0.5 bg-blue-400 rounded"></span>
                {{ __('app.max_weight_per_session') }}
            </span>
            @if($pr?->max_weight)
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-5 border-b-2 border-dashed border-yellow-400"></span>
                    {{ __('app.personal_record') }} ({{ $pr->max_weight }} kg)
                </span>
            @endif
        </div>

        {{-- Lista de sessões --}}
        <div class="border border-zinc-800 rounded-2xl overflow-hidden divide-y divide-zinc-800">
            @foreach($history as $we)
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-400">
                            {{ $we->workout->started_at->format('d M Y') }}
                            · {{ $we->workout->routine->name ?? '' }}
                        </span>
                        <span class="text-xs text-zinc-500">
                            {{ $we->sets->count() }} sets
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($we->sets as $set)
                            @php
                                $isPR = $pr && $set->weight >= $pr->max_weight && $set->reps >= ($pr->reps_at_max_weight ?? 0);
                            @endphp
                            <span class="text-xs px-3 py-1.5 rounded-full
                                {{ $isPR
                                    ? 'bg-yellow-500/20 text-yellow-300 ring-1 ring-yellow-500/40'
                                    : 'bg-zinc-800 text-zinc-300' }}">
                                {{ $set->weight }} kg × {{ $set->reps }}
                                @if($isPR) 🏆 @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    @endif

</div>

@if($history->isNotEmpty())
@script
<script>
    const labels    = @json($chartData->pluck('label'));
    const data      = @json($chartData->pluck('max'));
    const pr        = {{ $pr?->max_weight ?? 'null' }};
    const gridColor = 'rgba(255,255,255,0.07)';
    const textColor = '#888';
    const chartKey  = 'historyChart-{{ $exerciseId }}';

    if (window['_chart_' + chartKey]) {
        window['_chart_' + chartKey].destroy();
        delete window['_chart_' + chartKey];
    }

    const ctx = document.getElementById(chartKey);
    if (ctx && window.Chart) {
        const datasets = [{
            label: '{{ __("app.max_weight_per_session") }}',
            data,
            borderColor: '#378ADD',
            backgroundColor: 'rgba(55,138,221,0.12)',
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: data.map(v => pr && v >= pr ? '#EF9F27' : '#378ADD'),
            tension: 0.35,
            fill: true,
        }];

        if (pr) {
            datasets.push({
                label: 'PR',
                data: Array(labels.length).fill(pr),
                borderColor: '#EF9F27',
                borderWidth: 1.5,
                borderDash: [5, 4],
                pointRadius: 0,
                fill: false,
            });
        }

        window['_chart_' + chartKey] = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: v => v.raw + ' kg' } }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 11 } },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 11 }, maxTicksLimit: 4, callback: v => v + ' kg' },
                        border: { display: false }
                    }
                }
            }
        });
    }
</script>
@endscript
@endif
