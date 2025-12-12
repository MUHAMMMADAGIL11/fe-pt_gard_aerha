@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-[26px] font-bold text-white mt-3">Daftar Barang</h1>
                <p class="text-[14px] text-slate-300 mt-1.5">Kelola stok, kategori, dan detail barang dengan cepat.</p>
            </div>
            <a href="{{ route('barang.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 text-[13px] font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                + Tambah Barang
            </a>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-7 py-4 font-semibold">Kode</th>
                            <th class="px-7 py-4 font-semibold">Nama Barang</th>
                            <th class="px-7 py-4 font-semibold">Kategori</th>
                            <th class="px-7 py-4 font-semibold text-right">Stok</th>
                            <th class="px-7 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($barang as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 font-semibold text-white">{{ $item->kode_barang }}</td>
                                <td class="px-7 py-3.5">
                                    <p class="font-semibold text-white">{{ $item->nama_barang }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Stok minimum: {{ $item->stok_minimum }}</p>
                                </td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                <td class="px-7 py-3.5 text-right">
                                    <span
                                        class="px-3 py-1 text-[12px] font-semibold rounded-full {{ $item->stok < $item->stok_minimum ? 'bg-rose-100/80 text-rose-700' : 'bg-emerald-100/80 text-emerald-700' }}">
                                        {{ $item->stok }}
                                    </span>
                                </td>
                                <td class="px-7 py-3.5 text-right space-x-2">
                                    <a href="{{ route('barang.edit', $item->id_barang) }}"
                                        class="inline-flex items-center rounded-md border border-blue-200/40 px-3 py-1 text-[12px] font-semibold text-blue-200 hover:bg-blue-200/10">Edit</a>
                                    @if(auth()->user()->hasRole(['AdminGudang', 'KepalaDivisi']))
                                        <button type="button" data-delete data-name="{{ $item->nama_barang }}"
                                            data-action="{{ route('barang.destroy', $item->id_barang) }}"
                                            class="inline-flex items-center rounded-md border border-rose-200/40 px-3 py-1 text-[12px] font-semibold text-rose-200 hover:bg-rose-200/10">
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="5">Belum ada data barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $barang->links() }}
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
                    const name = button.dataset.name ?? 'barang ini';
                    const action = button.dataset.action;
                    modalText.textContent = `Anda akan menghapus ${name}. Tindakan ini tidak dapat dibatalkan.`;
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
