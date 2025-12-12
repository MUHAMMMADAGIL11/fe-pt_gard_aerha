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
                                    @if(auth()->user()?->hasRole('KepalaDivisi'))
                                        <a href="{{ route('user.edit', $user->id_user) }}"
                                            class="inline-flex items-center rounded-md border border-blue-200/40 px-3 py-1 text-[12px] font-semibold text-blue-200 hover:bg-blue-200/10">Edit</a>
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

    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative max-w-md mx-auto mt-40 bg-white rounded-2xl shadow-2xl p-6 space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-rose-500 uppercase tracking-wide">Konfirmasi Hapus</p>
                <h2 class="text-xl font-bold text-slate-900">Yakin ingin menghapus?</h2>
                <p id="deleteModalText" class="text-sm text-slate-500">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:justify-end gap-2">
                <button id="cancelDelete"
                    class="inline-flex justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <form method="POST" id="deleteForm" data-loading="true">
                    @csrf
                    @method('DELETE')
                    @include('components.button', [
                        'label' => 'Hapus',
                        'type' => 'submit',
                        'variant' => 'danger',
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('deleteModal');
            const modalText = document.getElementById('deleteModalText');
            const deleteForm = document.getElementById('deleteForm');
            const cancelButton = document.getElementById('cancelDelete');

            const closeModal = () => modal.classList.add('hidden');
            const openModal = () => modal.classList.remove('hidden');

            document.querySelectorAll('[data-delete]').forEach((button) => {
                button.addEventListener('click', () => {
                    const name = button.dataset.name ?? 'user ini';
                    const action = button.dataset.action;
                    modalText.textContent = `Anda akan menghapus user "${name}". Tindakan ini tidak dapat dibatalkan.`;
                    deleteForm.setAttribute('action', action);
                    openModal();
                });
            });

            cancelButton?.addEventListener('click', () => closeModal());
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
@endpush

