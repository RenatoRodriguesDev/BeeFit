<div class="relative" x-data="{ open: false }">

    {{-- Bell button --}}
    <button @click="open = !open"
        class="relative flex items-center justify-center w-9 h-9 rounded-xl hover:bg-zinc-800 transition">
        <span class="text-lg leading-none">🔔</span>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center leading-none">
                {{ $unreadCount > 9 ? "9+" : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
        x-transition
        @click.away="open = false"
        class="absolute right-0 bottom-full mb-2 w-80 bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl z-50 overflow-hidden"
        style="display: none;">

        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800">
            <span class="text-sm font-semibold text-white">{{ __("app.notifications") }}</span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead"
                    class="text-xs text-zinc-400 hover:text-white transition">
                    {{ __("app.mark_all_read") }}
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-zinc-800">
            @forelse($notifications as $notif)
                @php $data = $notif->data; $isUnread = $notif->read_at === null; @endphp

                @if($data["type"] === "follow_requested")
                    <div class="flex items-start gap-3 px-4 py-3 {{ $isUnread ? "bg-zinc-800/50" : "" }}">
                        <a href="{{ route("social.profile", $data["user_id"]) }}"
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0 mt-0.5">
                            @if($data["user_avatar"])
                                <img src="{{ asset("storage/" . $data["user_avatar"]) }}" class="w-full h-full object-cover">
                            @else
                                {{ $data["user_initials"] }}
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-zinc-200 leading-snug">
                                <a href="{{ route("social.profile", $data["user_id"]) }}" class="font-semibold hover:underline">{{ $data["user_name"] }}</a>
                                {{ __("app.notif_follow_requested") }}
                            </p>
                            <p class="text-[10px] text-zinc-600 mt-0.5 mb-2">{{ $notif->created_at->diffForHumans() }}</p>
                            <div class="flex gap-2">
                                <button wire:click="acceptFollow({{ $data["follow_id"] }}, {{ chr(39) }}{{ $notif->id }}{{ chr(39) }})"
                                    class="px-3 py-1 text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition">
                                    {{ __("app.accept") }}
                                </button>
                                <button wire:click="rejectFollow({{ $data["follow_id"] }}, {{ chr(39) }}{{ $notif->id }}{{ chr(39) }})"
                                    class="px-3 py-1 text-xs font-semibold bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-lg transition">
                                    {{ __("app.decline") }}
                                </button>
                            </div>
                        </div>
                        @if($isUnread)
                            <span class="w-2 h-2 bg-blue-400 rounded-full mt-1.5 shrink-0"></span>
                        @endif
                    </div>
                @else
                    <a href="{{ route("social.profile", $data["user_id"]) }}"
                        wire:click="markRead({{ chr(39) }}{{ $notif->id }}{{ chr(39) }})"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-zinc-800 transition {{ $isUnread ? "bg-zinc-800/50" : "" }}">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0 mt-0.5">
                            @if($data["user_avatar"])
                                <img src="{{ asset("storage/" . $data["user_avatar"]) }}" class="w-full h-full object-cover">
                            @else
                                {{ $data["user_initials"] }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-zinc-200 leading-snug">
                                <span class="font-semibold">{{ $data["user_name"] }}</span>
                                @if($data["type"] === "follow_accepted")
                                    {{ __("app.notif_follow_accepted") }}
                                @elseif($data["type"] === "post_liked")
                                    {{ __("app.notif_post_liked") }}
                                @elseif($data["type"] === "post_commented")
                                    {{ __("app.notif_post_commented") }}
                                    @if(!empty($data["preview"]))
                                        <span class="text-zinc-500 italic">— "{{ $data["preview"] }}"</span>
                                    @endif
                                @endif
                            </p>
                            <p class="text-[10px] text-zinc-600 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if($isUnread)
                            <span class="w-2 h-2 bg-blue-400 rounded-full mt-1.5 shrink-0"></span>
                        @endif
                    </a>
                @endif
            @empty
                <div class="px-4 py-8 text-center text-zinc-600 text-sm">
                    {{ __("app.no_notifications") }}
                </div>
            @endforelse
        </div>
    </div>
</div>
