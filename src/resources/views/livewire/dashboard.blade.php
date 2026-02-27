<div class="bg-zinc-600 p-6 rounded-3xl w-[360px]">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">

        <button wire:click="previousMonth" class="w-8 h-8 flex items-center justify-center 
                   bg-zinc-800 rounded-full hover:bg-zinc-700">
            ‹
        </button>

        <h2 class="text-sm font-semibold tracking-wide">
            {{ $currentMonth->translatedFormat('F Y') }}
        </h2>

        <button wire:click="nextMonth" class="w-8 h-8 flex items-center justify-center 
                   bg-zinc-800 rounded-full hover:bg-zinc-700">
            ›
        </button>

    </div>

    {{-- Week Days --}}
    <div class="grid grid-cols-7 text-xs text-zinc-500 mb-2 text-center">
        <div>{{ __('app.sunday') }}</div>
        <div>{{ __('app.monday') }}</div>
        <div>{{ __('app.tuesday') }}</div>
        <div>{{ __('app.wednesday') }}</div>
        <div>{{ __('app.thursday') }}</div>
        <div>{{ __('app.friday') }}</div>
        <div>{{ __('app.saturday') }}</div>
    </div>

    {{-- Days --}}
    <div class="grid grid-cols-7 gap-y-2 text-sm text-center">

        @php
            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();
            $startDayOfWeek = $startOfMonth->dayOfWeek;
        @endphp

        {{-- Empty spaces --}}
        @for ($i = 0; $i < $startDayOfWeek; $i++)
            <div></div>
        @endfor

        @for ($day = 1; $day <= $endOfMonth->day; $day++)

            @php
                $date = $currentMonth->copy()->day($day);
                $dateKey = $date->format('Y-m-d');
                $hasWorkout = array_key_exists($dateKey, $workoutsByDate);
                $isToday = $date->isToday();
                $isSelected = $selectedDate === $dateKey;
            @endphp

            <div wire:click="selectDate('{{ $dateKey }}')" class="relative flex items-center justify-center 
                            w-9 h-9 mx-auto cursor-pointer 
                            rounded-full transition
                            {{ $isSelected ? 'bg-blue-600 text-white' : '' }}
                            {{ !$isSelected && $isToday ? 'border border-blue-500' : '' }}
                            hover:bg-zinc-800">

                {{ $day }}

                {{-- Small workout dot --}}
                @if($hasWorkout)
                    <span class="absolute bottom-1 w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                @endif

            </div>

        @endfor

    </div>

    @if(!empty($selectedWorkouts))
    
    <div class="mt-6 space-y-3 w-auto">
        
        @foreach($selectedWorkouts as $workout)
        
        <a href="{{ route('workouts.show', $workout) }}" class="block bg-zinc-600 p-4 rounded-xl hover:bg-zinc-800">
            
            <div class="font-semibold">
                {{ $workout->routine->name }}
            </div>
            
            <div class="text-sm text-zinc-400">
                {{ $workout->started_at->format('H:i') }}
            </div>
            
        </a>
        
        @endforeach
        
    </div>
    
    @endif
</div>