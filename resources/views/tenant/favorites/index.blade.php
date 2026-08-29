@extends('layouts.tenant', ['title' => 'Kost & Properti Favorit', 'headerTitle' => 'Favorit Saya'])

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Kost & Properti Disimpan</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar hunian kost dan apartemen yang Anda simpan untuk ditinjau nanti.</p>
    </div>

    @if($favorites->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Belum Ada Properti Favorit</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Klik ikon hati pada listing properti di katalog untuk menyimpannya ke daftar ini.</p>
            </div>
            <x-button variant="primary" href="{{ route('properties.index') }}">
                Jelajahi Katalog Kost
            </x-button>
        </x-card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $property)
                <div class="relative">
                    <x-property-card :property="$property" />
                    
                    <!-- Remove from wishlist button -->
                    <form action="{{ route('tenant.favorites.toggle', $property) }}" method="POST" class="absolute top-3 right-3 z-20">
                        @csrf
                        <button
                            type="submit"
                            class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs text-rose-500 hover:text-rose-600 flex items-center justify-center shadow-md hover:scale-110 transition-transform"
                            title="Hapus dari Favorit"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@endsection
