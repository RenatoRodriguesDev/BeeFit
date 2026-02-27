<div>
    @if($activeWorkout)

        <div x-data="{
                        start: new Date('{{ $activeWorkout->started_at->toIso8601String() }}'),
                        now: new Date(),
                        diff: 0,
                        interval: null,
                        updateSpacing() {
                            const content = document.getElementById('app-content');
                            if (!content) return;
                            content.style.paddingTop = '96px';
                        },
                        formatTime(seconds) {
                            let h = Math.floor(seconds / 3600);
                            let m = Math.floor((seconds % 3600) / 60);
                            let s = seconds % 60;

                            return String(h).padStart(2,'0') + ':' +
                                String(m).padStart(2,'0') + ':' +
                                String(s).padStart(2,'0');
                        }
                    }" x-init="
                        interval = setInterval(() => {
                            now = new Date();
                            diff = Math.floor((now - start) / 1000);
                        }, 1000);
                        updateSpacing();
                    " class="fixed top-0 left-0 right-0 z-50 
                        bg-blue-600/30 text-white
                        px-6 py-4
                        flex items-center justify-between
                        shadow-lg md:left-64">

            <div>
                <div class="text-xs opacity-80">
                    Treino em progresso
                </div>

                <div class="font-semibold">
                    {{ $activeWorkout->routine->name }}
                </div>
            </div>

            <div class="flex items-center gap-6">

                <div class="text-lg font-mono" x-text="formatTime(diff)">
                </div>

                <a href="{{ route('workouts.session', $activeWorkout) }}"
                    class="bg-white text-blue-600 px-4 py-2 rounded-xl text-sm font-semibold">
                    Continuar
                </a>

            </div>

        </div>

    @endif
</div>