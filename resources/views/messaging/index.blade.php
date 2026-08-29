@php
    $layout = auth()->user()->isOwner() ? 'layouts.owner' : 'layouts.tenant';
@endphp

@extends($layout, ['title' => 'Pusat Pesan & Chat — Rentiva', 'headerTitle' => 'Pusat Pesan & Chat'])

@section('content')
<div class="h-[calc(100vh-12rem)] flex flex-col md:flex-row bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden" x-data="{ mobileChatOpen: {{ $activeConversation ? 'true' : 'false' }} }">
    <!-- Conversation Sidebar -->
    <div
        class="w-full md:w-80 lg:w-96 border-r border-slate-200/80 flex flex-col bg-slate-50/50"
        :class="{ 'hidden md:flex': mobileChatOpen && {{ $activeConversation ? 'true' : 'false' }} }"
    >
        <!-- Sidebar Search / Header -->
        <div class="p-4 border-b border-slate-200/80 bg-white">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Kotak Masuk Pesan</h2>
            <p class="text-[11px] text-slate-500 mt-0.5">Komunikasi langsung penyewa & pemilik kost</p>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
            @if($conversations->isEmpty())
                <div class="p-8 text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Belum Ada Percakapan</p>
                    <p class="text-[11px] text-slate-400">Pesan dari penyewa atau pertanyaan kost akan tampil di sini.</p>
                </div>
            @else
                @foreach($conversations as $conv)
                    @php
                        $other = $conv->getOtherParticipant(auth()->user());
                        $isActive = $activeConversation && $activeConversation->id === $conv->id;
                        $isUnread = $conv->isUnreadFor(auth()->user());
                    @endphp
                    <a
                        href="{{ route('messages.index', ['conversation' => $conv->id]) }}"
                        class="flex items-start gap-3 p-4 transition-colors relative {{ $isActive ? 'bg-white shadow-xs border-l-4 border-emerald-600' : 'hover:bg-slate-100/70' }}"
                        @click="mobileChatOpen = true"
                    >
                        <x-avatar :name="$other?->name ?? 'User'" size="md" />

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <h3 class="text-xs font-bold text-slate-900 truncate">
                                    {{ $other?->name ?? 'Pengguna' }}
                                </h3>
                                @if($conv->last_message_at)
                                    <span class="text-[10px] text-slate-400 shrink-0">
                                        {{ $conv->last_message_at->diffForHumans(null, true) }}
                                    </span>
                                @endif
                            </div>

                            @if($conv->property)
                                <p class="text-[10px] font-semibold text-emerald-700 truncate mb-1">
                                    📍 {{ $conv->property->name }}
                                </p>
                            @endif

                            <p class="text-[11px] {{ $isUnread ? 'font-bold text-slate-900' : 'text-slate-500' }} truncate">
                                {{ $conv->latestMessage?->body ?? 'Memulai obrolan...' }}
                            </p>
                        </div>

                        @if($isUnread)
                            <span class="w-2.5 h-2.5 bg-emerald-600 rounded-full shrink-0 self-center"></span>
                        @endif
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Active Chat Pane -->
    <div
        class="flex-1 flex flex-col bg-white"
        :class="{ 'hidden md:flex': !mobileChatOpen && {{ $activeConversation ? 'true' : 'false' }} }"
    >
        @if($activeConversation)
            @php
                $other = $activeConversation->getOtherParticipant(auth()->user());
            @endphp
            <!-- Chat Header -->
            <div class="h-16 px-6 border-b border-slate-200/80 flex items-center justify-between bg-white z-10">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="md:hidden text-slate-500 hover:text-slate-800 p-1"
                        @click="mobileChatOpen = false"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <x-avatar :name="$other?->name ?? 'User'" size="md" status="online" />
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-none">{{ $other?->name ?? 'Pengguna' }}</h3>
                        @if($activeConversation->property)
                            <a href="{{ route('properties.show', $activeConversation->property->slug) }}" target="_blank" class="text-[10px] text-emerald-600 font-semibold hover:underline">
                                Properti: {{ $activeConversation->property->name }} &nearr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/40" id="chat-messages-container">
                @if($messages->isEmpty())
                    <div class="text-center py-12 text-slate-400 text-xs">
                        Belum ada riwayat pesan. Mulai percakapan pertama Anda!
                    </div>
                @else
                    @foreach($messages as $msg)
                        @php
                            $isMe = $msg->sender_id === auth()->id();
                        @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="max-w-md lg:max-w-lg space-y-1">
                                <div class="px-4 py-3 rounded-2xl text-xs sm:text-sm {{ $isMe ? 'bg-emerald-600 text-white rounded-br-xs shadow-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs shadow-xs' }}">
                                    <p class="whitespace-pre-line leading-relaxed">{{ $msg->body }}</p>

                                    @if($msg->hasAttachment())
                                        <div class="mt-2.5 pt-2 border-t {{ $isMe ? 'border-emerald-500/50' : 'border-slate-100' }}">
                                            @if($msg->isImageAttachment())
                                                <a href="{{ $msg->attachment_url }}" target="_blank" class="block rounded-xl overflow-hidden border border-black/10">
                                                    <img src="{{ $msg->attachment_url }}" alt="Attachment" class="max-h-48 rounded-xl object-cover" />
                                                </a>
                                            @else
                                                <a href="{{ $msg->attachment_url }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-semibold {{ $isMe ? 'text-emerald-100 hover:text-white' : 'text-emerald-700 hover:text-emerald-800' }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Unduh Lampiran Dokumen ({{ $msg->formatted_attachment_size }})
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="text-[10px] text-slate-400 px-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                                    {{ $msg->created_at->format('H:i') }} &bull; {{ $msg->created_at->format('d M') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Message Input Bar -->
            <div class="p-4 bg-white border-t border-slate-200/80">
                <form action="{{ route('messages.send', $activeConversation) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <div class="flex items-end gap-2">
                        <div class="relative flex-1">
                            <textarea
                                name="body"
                                rows="2"
                                placeholder="Ketik pesan Anda di sini..."
                                class="w-full text-xs sm:text-sm border border-slate-300 rounded-2xl p-3 pr-10 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                                required
                            ></textarea>
                            <label class="absolute right-3 bottom-3 text-slate-400 hover:text-emerald-600 cursor-pointer" title="Lampirkan Gambar atau PDF">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" />
                            </label>
                        </div>

                        <button
                            type="submit"
                            class="p-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl shadow-md shadow-emerald-600/20 transition-all shrink-0 cursor-pointer"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Empty Chat State -->
            <div class="flex-1 flex items-center justify-center p-8 text-center text-slate-400">
                <div class="space-y-3">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Pilih Percakapan</p>
                    <p class="text-xs max-w-sm">Pilih salah satu obrolan dari panel sebelah kiri untuk membaca dan membalas pesan.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
