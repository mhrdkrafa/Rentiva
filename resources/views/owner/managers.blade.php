@extends('layouts.owner', ['title' => 'Manajer & Pengelola Properti', 'headerTitle' => 'Penugasan Pengelola'])

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Manajer & Asisten Pengelola Properti</h2>
            <p class="text-sm text-slate-500 mt-1">Berikan akses operasional kepada staf atau penjaga kost untuk mengelola kamar dan meninjau booking.</p>
        </div>
    </div>

    <!-- Form to Assign New Manager -->
    <x-card class="p-6 sm:p-8 space-y-6">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Tugaskan Manajer / Staf Baru
        </h3>

        <form action="{{ route('owner.managers.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input
                    name="manager_email"
                    label="Alamat Email Pengguna"
                    placeholder="email.staf@example.com"
                    required
                    helper="Pastikan staf Anda telah mendaftar akun di Rentiva."
                />

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Izin yang Diberikan</label>
                    <div class="space-y-2 pt-1">
                        <x-checkbox name="permissions[]" value="manage_assigned_units" label="Kelola status dan foto kamar/unit" />
                        <x-checkbox name="permissions[]" value="review_assigned_bookings" label="Tinjau & konfirmasi permintaan booking sewa" />
                        <x-checkbox name="permissions[]" value="manage_assigned_availability" label="Atur ketersediaan kalender sewa" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary" size="md">
                    Tugaskan Pengelola
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- List of Assigned Managers -->
    <x-card class="p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Pengelola yang Ditugaskan</h3>
            <span class="text-xs text-slate-500 font-medium">{{ $assignments->count() }} Pengelola Aktif</span>
        </div>

        @if($assignments->isEmpty())
            <div class="p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum Ada Manajer Properti yang Ditugaskan</p>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda dapat menambahkan staf atau pengurus kost untuk membantu operasional harian.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($assignments as $assignment)
                    <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$assignment->manager->name" size="md" />
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $assignment->manager->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $assignment->manager->email }} &bull; {{ $assignment->manager->phone ?? 'Tanpa nomor telepon' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <x-badge :variant="$assignment->status === 'active' ? 'success' : 'neutral'" size="sm">
                                        {{ ucfirst($assignment->status) }}
                                    </x-badge>
                                    <span class="text-[11px] text-slate-400">Ditugaskan: {{ $assignment->assigned_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($assignment->status === 'active')
                            <form action="{{ route('owner.managers.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mencabut hak kelola pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger" size="sm">
                                    Cabut Akses
                                </x-button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
@endsection
