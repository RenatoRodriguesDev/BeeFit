<div class="max-w-4xl mx-auto py-12 space-y-8">

    <h1 class="text-3xl font-bold">
        {{ $workout->routine->name }}
    </h1>

    <p class="text-zinc-400">
        {{ $workout->started_at->format('d M Y') }}
    </p>

    @foreach($workout->exercises as $exercise)

        <div class="bg-zinc-900 p-6 rounded-3xl space-y-4">

            <h2 class="text-xl font-semibold">
                {{ $exercise->exercise->translate()->name }}
            </h2>

            @foreach($exercise->sets as $set)

                <div class="flex gap-6">
                    <div>{{ $set->set_number }}</div>
                    <div>{{ $set->weight }} kg</div>
                    <div>{{ $set->reps }} reps</div>
                </div>

            @endforeach

        </div>

    @endforeach

</div>