<div class="space-y-6">

    {{-- Resumo geral --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-zinc-800 rounded-2xl p-4 text-center">
            <div class="text-3xl font-bold text-white">{{ $totals['count'] }}</div>
            <div class="text-xs text-zinc-400 mt-1">{{ __('app.exercises_tracked') }}</div>
        </div>
        <div class="bg-zinc-800 rounded-2xl p-4 text-center">
            <div class="text-3xl font-bold text-yellow-400">{{ number_format($totals['best_1rm'], 1) }}</div>
            <div class="text-xs text-zinc-400 mt-1">{{ __('app.best_1rm') }} (kg)</div>
        </div>
        <div class="bg-zinc-800 rounded-2xl p-4 text-center">
            <div class="text-3xl font-bold text-white">{{ number_format($totals['max_weight'], 1) }}</div>
            <div class="text-xs text-zinc-400 mt-1">{{ __('app.heaviest_lift') }} (kg)</div>
        </div>
        <div class="bg-zinc-800 rounded-2xl p-4 text-center">
            <div class="text-3xl font-bold text-white">{{ $totals['max_reps'] }}</div>
            <div class="text-xs text-zinc-400 mt-1">{{ __('app.most_reps') }}</div>
        </div>
    </div>

    {{-- Pesquisa --}}
    <input
        type="text"
        wire:model.live.debounce.300ms="search"
        placeholder="{{ __('app.browse_exercises') }}"
        class="w-full bg-zinc-800 rounded-xl p-3 border border-zinc-700 text-white placeholder-zinc-500
               focus:outline-none focus:border-blue-500 transition">

    @if($records->isEmpty())
        <div class="bg-zinc-800 rounded-3xl p-10 text-center space-y-2">
            <div class="text-4xl">🏆</div>
            <p class="text-zinc-300 font-medium">{{ __('app.no_records_yet') }}</p>
            <p class="text-sm text-zinc-500">{{ __('app.finish_workout_to_get_records') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($records as $pr)
                @php
                    $cData   = $chartData[$pr->exercise_id] ?? ['labels' => [], 'data' => []];
                    $chartId = 'stat-chart-' . $pr->exercise_id;
                    $isTop   = $loop->first;
                @endphp

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden flex flex-col
                    {{ $isTop ? 'ring-1 ring-yellow-500/40' : '' }}">

                    <div class="px-4 pt-4 pb-2 flex justify-between items-start">
                        <div>
                            <span class="font-semibold text-white">
                                {{ $pr->exercise?->translate()?->name ?? 'Exercise' }}
                            </span>
                            @if($isTop)
                                <span class="ml-2 text-[10px] bg-yellow-500/20 text-yellow-300
                                             px-2 py-0.5 rounded-full ring-1 ring-yellow-500/40">
                                    PR
                                </span>
                            @endif
                        </div>
                        @if($pr->workout)
                            <span class="text-xs text-zinc-500">
                                {{ $pr->workout->started_at->format('d M Y') }}
                            </span>
                        @endif
                    </div>

                    @if(count($cData['data']) > 1)
                        <div class="px-3 pb-1" style="height:88px;position:relative">
                            <canvas id="{{ $chartId }}"></canvas>
                        </div>
                    @else
                        <div class="px-4 py-3 text-xs text-zinc-600 italic">{{ __('app.need_more_sessions') }}</div>
                    @endif

                    <div class="grid grid-cols-2 border-t border-zinc-800 mt-auto">
                        <div class="p-3 border-r border-zinc-800">
                            <div class="text-xs text-zinc-400">{{ __('app.pr_max_weight') }}</div>
                            <div class="font-semibold text-white mt-0.5">{{ $pr->max_weight }} kg</div>
                            <div class="text-xs text-zinc-500">× {{ $pr->reps_at_max_weight }} reps</div>
                        </div>
                        <div class="p-3">
                            <div class="text-xs text-zinc-400">{{ __('app.pr_1rm') }}</div>
                            <div class="font-semibold text-yellow-400 mt-0.5">{{ $pr->estimated_1rm }} kg</div>
                            <div class="text-xs text-zinc-500">Epley</div>
                        </div>
                        <div class="p-3 border-t border-r border-zinc-800">
                            <div class="text-xs text-zinc-400">{{ __('app.pr_max_volume') }}</div>
                            <div class="font-semibold text-white mt-0.5">{{ number_format($pr->max_volume_set, 0) }} kg</div>
                            <div class="text-xs text-zinc-500">{{ __('app.pr_single_set') }}</div>
                        </div>
                        <div class="p-3 border-t border-zinc-800">
                            <div class="text-xs text-zinc-400">{{ __('app.pr_max_reps') }}</div>
                            <div class="font-semibold text-white mt-0.5">{{ $pr->max_reps }} reps</div>
                            <div class="text-xs text-zinc-500">@ {{ $pr->weight_at_max_reps }} kg</div>
                        </div>
                    </div>
                </div>

                @if(count($cData['data']) > 1)
                <script>
                (function(){
                    const id     = '{{ $chartId }}';
                    const labels = @json($cData['labels']);
                    const data   = @json($cData['data']);
                    const prVal  = {{ $pr->max_weight ?? 'null' }};
                    const isDark = window.matchMedia('(prefers-color-scheme:dark)').matches;
                    const grid   = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
                    const tick   = isDark ? '#666' : '#999';
                    function init(){
                        const ctx = document.getElementById(id);
                        if(!ctx||!window.Chart){ setTimeout(init,120); return; }
                        new Chart(ctx,{
                            type:'line',
                            data:{labels,datasets:[
                                {data,borderColor:'#378ADD',backgroundColor:'rgba(55,138,221,0.1)',
                                 borderWidth:2,pointRadius:3,tension:0.35,fill:true,
                                 pointBackgroundColor:data.map(v=>prVal&&v>=prVal?'#EF9F27':'#378ADD')},
                                {data:Array(labels.length).fill(prVal),borderColor:'#EF9F27',
                                 borderWidth:1.5,borderDash:[4,3],pointRadius:0,fill:false}
                            ]},
                            options:{responsive:true,maintainAspectRatio:false,
                                plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>v.raw+' kg'}}},
                                scales:{
                                    x:{display:false},
                                    y:{grid:{color:grid},ticks:{color:tick,font:{size:9},maxTicksLimit:3,callback:v=>v+'kg'},border:{display:false}}
                                }
                            }
                        });
                    }
                    init();
                })();
                </script>
                @endif
            @endforeach
        </div>
    @endif

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>