@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-slate-500 font-semibold">Barang</p>
                <h1 class="text-2xl font-bold text-slate-900">Tambah Barang</h1>
                <p class="text-sm text-slate-500">Masukkan detail lengkap untuk barang baru.</p>
            </div>
            <a href="{{ route('barang.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('barang.store') }}" class="space-y-5" data-loading="true">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select name="id_kategori"
                            class="w-full rounded-lg border {{ $errors->has('id_kategori') ? 'border-rose-300' : 'border-slate-300' }} py-2 px-3 text-sm focus:border-blue-600 focus:ring-blue-600">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_kategori }}"
                                    {{ old('id_kategori') == $category->id_kategori ? 'selected' : '' }}>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @include('components.input', [
                        'label' => 'Kode Barang',
                        'name' => 'kode_barang',
                        'value' => old('kode_barang'),
                        'required' => true,
                        'helper' => 'Gunakan kode unik untuk memudahkan pencarian.',
                        'error' => $errors->first('kode_barang'),
                    ])

                    @include('components.input', [
                        'label' => 'Nama Barang',
                        'name' => 'nama_barang',
                        'value' => old('nama_barang'),
                        'required' => true,
                        'error' => $errors->first('nama_barang'),
                    ])

                    @include('components.input', [
                        'label' => 'Stok Saat Ini',
                        'name' => 'stok',
                        'type' => 'number',
                        'value' => old('stok', 0),
                        'required' => true,
                        'error' => $errors->first('stok'),
                    ])

                    @include('components.input', [
                        'label' => 'Stok Minimum',
                        'name' => 'stok_minimum',
                        'type' => 'number',
                        'value' => old('stok_minimum', 0),
                        'required' => true,
                        'error' => $errors->first('stok_minimum'),
                    ])
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('barang.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
                    @include('components.button', [
                        'label' => 'Simpan',
                        'type' => 'submit',
                    ])
                </div>
            </form>
        </div>
    </div>
@endsection

