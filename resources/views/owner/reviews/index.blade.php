@extends('layouts.owner', ['title' => 'Ulasan & Rating Properti'])

@section('owner_content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Ulasan & Rating Penyewa</h1>
        <p class="text-xs text-slate-500 mt-1">Lihat pengalaman penyewa dan berikan balasan resmi untuk membangun reputasi properti Anda.</p>
    </div>

    @if(session('success'))
        <x-alert variant="success" :message="session('success')" />
    @endif

    @if($reviews->isEmpty())
        <x-card class="p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                ⭐
            </div>
            <h3 class="text-sm font-bold text-slate-900">Belum Ada Ulasan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Ulasan dari penyewa yang telah menempati properti Anda akan muncul di sini.</p>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($reviews as $rev)
                <x-card class="p-6 space-y-4 bg-white" x-data="{ replyOpen: false }">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <x-avatar :name="$rev->tenant->name" size="md" />
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900">{{ $rev->tenant->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $rev->property->name }} &bull; {{ $rev->unit->name }}</p>
                                <div class="flex items-center gap-1 text-amber-400 text-xs">
                                    {{ str_repeat('⭐', $rev->rating) }}
                                    <span class="text-slate-400 text-[11px] ml-1">({{ $rev->rating }}/5)</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-xs text-slate-400">
                            {{ $rev->created_at->format('d M Y') }}
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        "{{ $rev->comment }}"
                    </p>

                    <!-- Owner reply display or reply button -->
                    @if($rev->owner_reply)
                        <div class="pl-4 border-l-2 border-emerald-500 space-y-1 bg-emerald-50/50 p-3 rounded-r-xl">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold text-emerald-800">Balasan Anda (Pemilik):</span>
                                <span class="text-slate-400">{{ $rev->owner_replied_at?->format('d M Y') }}</span>
                            </div>
                            <p class="text-xs text-slate-700">{{ $rev->owner_reply }}</p>
                        </div>
                    @else
                        <div class="pt-2">
                            <button
                                type="button"
                                @click="replyOpen = !replyOpen"
                                class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <span x-text="replyOpen ? 'Tutup Form Balasan' : 'Balas Ulasan Ini'"></span>
                            </button>

                            <form
                                x-show="replyOpen"
                                action="{{ route('owner.reviews.reply', $rev) }}"
                                method="POST"
                                class="mt-3 space-y-3 p-4 bg-slate-50 rounded-2xl border border-slate-200"
                            >
                                @csrf
                                <label class="block text-[11px] font-bold text-slate-700">Tulis Tanggapan Resmi:</label>
                                <textarea
                                    name="owner_reply"
                                    rows="2"
                                    required
                                    placeholder="Terima kasih telah memilih properti kami. Kami senang Anda merasa nyaman..."
                                    class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                ></textarea>
                                <div class="flex justify-end gap-2">
                                    <x-button type="button" variant="outline" size="sm" @click="replyOpen = false">
                                        Batal
                                    </x-button>
                                    <x-button type="submit" variant="primary" size="sm">
                                        Kirim Balasan
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    @endif
                </x-card>
            @endforeach

            <div>
                {{ $reviews->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
