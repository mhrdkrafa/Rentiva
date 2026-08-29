<div
    x-data="{
        messages: [],
        add(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.messages.push({ id, message, type });
            setTimeout(() => this.remove(id), 5000);
        },
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    }"
    x-init="
        @if(session()->has('success'))
            add('{{ addslashes(session('success')) }}', 'success');
        @endif
        @if(session()->has('error'))
            add('{{ addslashes(session('error')) }}', 'error');
        @endif
        @if(session()->has('warning'))
            add('{{ addslashes(session('warning')) }}', 'warning');
        @endif
        @if(session()->has('info'))
            add('{{ addslashes(session('info')) }}', 'info');
        @endif
    "
    @flash.window="add($event.detail.message, $event.detail.type || 'success')"
    class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"
>
    <template x-for="item in messages" :key="item.id">
        <div
            x-show="true"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto p-4 rounded-2xl shadow-xl border flex items-start gap-3 bg-white"
            :class="{
                'border-emerald-200 bg-emerald-50/90 text-emerald-900': item.type === 'success',
                'border-rose-200 bg-rose-50/90 text-rose-900': item.type === 'error',
                'border-amber-200 bg-amber-50/90 text-amber-900': item.type === 'warning',
                'border-blue-200 bg-blue-50/90 text-blue-900': item.type === 'info'
            }"
        >
            <div class="shrink-0 mt-0.5">
                <template x-if="item.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="item.type === 'error'">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="item.type === 'warning'">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="item.type === 'info'">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
            </div>
            <div class="flex-1 text-sm font-medium" x-text="item.message"></div>
            <button @click="remove(item.id)" class="shrink-0 text-slate-400 hover:text-slate-600 rounded p-0.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
