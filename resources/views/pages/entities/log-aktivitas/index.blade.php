@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <style>
            input.custom-date[type="date"]::-webkit-calendar-picker-indicator { opacity: 0; }
        </style>
        <div>
            <h1 class="text-[26px] font-bold text-white mt-3">Log Aktivitas</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Lihat semua aktivitas yang dilakukan pengguna dalam sistem.</p>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="GET" action="{{ route('log-aktivitas.index') }}" class="space-y-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-white">Tanggal Mulai</label>
                        <div class="relative">
                            <input type="text" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" placeholder="DD/MM/YYYY"
                                onfocus="this.type='date'; this.showPicker && this.showPicker();"
                                onblur="if(!this.value) this.type='text';"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 pr-12 py-3 text-sm text-white placeholder:text-slate-300 placeholder:uppercase focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20 custom-date">
                            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-white/90"
                                onclick="const el=this.previousElementSibling; el.type='date'; el.showPicker && el.showPicker(); el.focus();" aria-label="Pilih tanggal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M16 2v4M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="tanggal_akhir" class="block text-sm font-semibold text-white">Tanggal Akhir</label>
                        <div class="relative">
                            <input type="text" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" placeholder="DD/MM/YYYY"
                                onfocus="this.type='date'; this.showPicker && this.showPicker();"
                                onblur="if(!this.value) this.type='text';"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 pr-12 py-3 text-sm text-white placeholder:text-slate-300 placeholder:uppercase focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20 custom-date">
                            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-white/90"
                                onclick="const el=this.previousElementSibling; el.type='date'; el.showPicker && el.showPicker(); el.focus();" aria-label="Pilih tanggal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M16 2v4M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 flex items-end">
                        <button type="submit"
                            class="w-full rounded-xl bg-[#B69364] px-4 py-3 text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-7 py-4 font-semibold">Tanggal</th>
                            <th class="px-7 py-4 font-semibold">User</th>
                            <th class="px-7 py-4 font-semibold">Aktivitas</th>
                            <th class="px-7 py-4 font-semibold">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 text-slate-300">
                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('d/m/Y') }}
                                </td>
                                <td class="px-7 py-3.5">
                                    <p class="font-semibold text-white">{{ $log->user->username ?? '-' }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">{{ $log->user->nama_lengkap ?? '-' }}</p>
                                </td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $log->aktivitas ?? '-' }}</td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $log->detail ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="4">Belum ada log aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
 @endsection
