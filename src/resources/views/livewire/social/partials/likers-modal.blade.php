@if(!is_null($likers))
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
        wire:click.self="{{ $onClose }}">
        <div class="bg-zinc-900 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-sm max-h-[60vh] flex flex-col">
            <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-zinc-800 shrink-0">
                <h3 class="font-semibold text-white text-sm">{{ $title }}</h3>
                <button wire:click="{{ $onClose }}" class="text-zinc-400 hover:text-white text-xl leading-none">✕</button>
            </div>
            <div class="overflow-y-auto p-4 space-y-3">
                @forelse($likers as $liker)
                    <a href="{{ route('social.profile', $liker['id']) }}"
                        class="flex items-center gap-3 hover:bg-zinc-800 rounded-xl p-2 transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-xs font-bold overflow-hidden shrink-0">
                            @if($liker['avatar_path'])
                                <img src="{{ asset('storage/' . $liker['avatar_path']) }}" class="w-full h-full object-cover">
                            @else
                                {{ $liker['initials'] }}
                            @endif
                        </div>
                        <span class="text-sm text-white">{{ $liker['name'] }}</span>
                    </a>
                @empty
                    <p class="text-xs text-zinc-500 text-center py-4">{{ __('app.no_likes_yet') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endif
