@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Audit Trail</p>
            <h1 class="text-[26px] font-bold text-white mt-3">Log Aktivitas</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Lihat semua aktivitas yang dilakukan pengguna dalam sistem.</p>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="GET" action="{{ route('log-aktivitas.index') }}" class="space-y-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-white">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
                    </div>

                    <div class="space-y-2">
                        <label for="tanggal_akhir" class="block text-sm font-semibold text-white">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
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
                            <th class="px-7 py-4 font-semibold">Waktu</th>
                            <th class="px-7 py-4 font-semibold">User</th>
                            <th class="px-7 py-4 font-semibold">Aktivitas</th>
                            <th class="px-7 py-4 font-semibold">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 text-slate-300">
                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('d/m/Y H:i:s') }}
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

