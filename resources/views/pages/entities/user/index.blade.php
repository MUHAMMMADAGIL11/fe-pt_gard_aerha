@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-[26px] font-bold text-white mt-3">Manajemen Pengguna</h1>
                <p class="text-[14px] text-slate-300 mt-1.5">Kelola akun pengguna sistem.</p>
            </div>
            @if(auth()->user()?->hasRole(['KepalaDivisi', 'AdminGudang']))
                <a href="{{ route('user.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 text-[13px] font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                    + Tambah User
                </a>
            @endif
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-7 py-4 font-semibold">Username</th>
                            <th class="px-7 py-4 font-semibold">Nama Lengkap</th>
                            <th class="px-7 py-4 font-semibold">Role</th>
                            <th class="px-7 py-4 font-semibold text-center">Status</th>
                            <th class="px-7 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 font-semibold text-white">{{ $user->username }}</td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $user->nama_lengkap ?? '-' }}</td>
                                <td class="px-7 py-3.5">
                                    @php
                                        $normalized = \App\Models\User::normalizeRole($user->role ?? null) ?? '';
                                        $roleLabelMap = [
                                            'AdminGudang' => 'Admin Gudang',
                                            'PetugasOperasional' => 'Petugas Operasional',
                                            'KepalaDivisi' => 'Kepala Divisi',
                                        ];
                                        $roleStyles = [
                                            'AdminGudang' => 'bg-blue-100/80 text-blue-700',
                                            'PetugasOperasional' => 'bg-green-100/80 text-green-700',
                                            'KepalaDivisi' => 'bg-purple-100/80 text-purple-700',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full {{ $roleStyles[$normalized] ?? 'bg-slate-100/80 text-slate-700' }}">
                                        {{ $roleLabelMap[$normalized] ?? 'Pengguna' }}
                                    </span>
                                </td>
                                <td class="px-7 py-3.5 text-center">
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full {{ $user->is_active ? 'bg-emerald-100/80 text-emerald-700' : 'bg-rose-100/80 text-rose-700' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-7 py-3.5 text-right space-x-2">
                                    {{-- Kepala Divisi: Full Access (Edit, Reset, Delete). --}}
                                    {{-- Admin Gudang: Create Only (Petugas), No Edit/Reset/Delete. --}}
                                    
                                    @if(auth()->user()?->hasRole('KepalaDivisi'))
                                        <a href="{{ route('user.edit', $user->id_user) }}"
                                            class="inline-flex items-center rounded-md border border-blue-200/40 px-3 py-1 text-[12px] font-semibold text-blue-200 hover:bg-blue-200/10">Edit</a>
                                    
                                        <form action="{{ route('user.reset-password', $user->id_user) }}" method="POST" class="inline reset-form" data-name="{{ $user->username }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded-md border border-yellow-200/40 px-3 py-1 text-[12px] font-semibold text-yellow-200 hover:bg-yellow-200/10">
                                                Reset
                                            </button>
                                        </form>

                                        @if($user->id_user !== auth()->user()->id_user)
                                            <button type="button" data-delete data-name="{{ $user->username }}"
                                                data-action="{{ route('user.destroy', $user->id_user) }}"
                                                class="inline-flex items-center rounded-md border border-rose-200/40 px-3 py-1 text-[12px] font-semibold text-rose-200 hover:bg-rose-200/10">
                                                Hapus
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="5">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Reset Password Confirmation
            document.querySelectorAll('.reset-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const name = form.dataset.name;
                    Swal.fire({
                        title: 'Reset Password?',
                        html: `Password untuk <b>${name}</b> akan direset menjadi <b>password123</b>.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#eab308',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
@endpush

