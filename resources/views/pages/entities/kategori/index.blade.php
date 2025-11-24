@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Master Data</p>
                <h1 class="text-[26px] font-bold text-white mt-3">Daftar Kategori</h1>
                <p class="text-[14px] text-slate-300 mt-1.5">Kelola kategori barang dengan cepat.</p>
            </div>
            <a href="{{ route('kategori.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 text-[13px] font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                + Tambah Kategori
            </a>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-7 py-4 font-semibold">Nama Kategori</th>
                            <th class="px-7 py-4 font-semibold text-center">Jumlah Barang</th>
                            <th class="px-7 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($kategori as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 font-semibold text-white">{{ $item->nama_kategori }}</td>
                                <td class="px-7 py-3.5 text-center">
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full bg-blue-100/80 text-blue-700">
                                        {{ $item->barang_count }} barang
                                    </span>
                                </td>
                                <td class="px-7 py-3.5 text-right space-x-2">
                                    <a href="{{ route('kategori.edit', $item->id_kategori) }}"
                                        class="inline-flex items-center rounded-md border border-blue-200/40 px-3 py-1 text-[12px] font-semibold text-blue-200 hover:bg-blue-200/10">Edit</a>
                                    <button type="button" data-delete data-name="{{ $item->nama_kategori }}"
                                        data-action="{{ route('kategori.destroy', $item->id_kategori) }}"
                                        class="inline-flex items-center rounded-md border border-rose-200/40 px-3 py-1 text-[12px] font-semibold text-rose-200 hover:bg-rose-200/10">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="3">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $kategori->links() }}
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
                    const name = button.dataset.name ?? 'kategori ini';
                    const action = button.dataset.action;
                    modalText.textContent = `Anda akan menghapus kategori "${name}". Tindakan ini tidak dapat dibatalkan.`;
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

