@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-slate-500 font-semibold">Barang</p>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Barang</h1>
                <p class="text-sm text-slate-500">Perbarui informasi barang sesuai kebutuhan.</p>
            </div>
            <a href="{{ route('barang.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('barang.update', $barang->id_barang) }}" class="space-y-5" data-loading="true">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select name="id_kategori"
                            class="w-full rounded-lg border {{ $errors->has('id_kategori') ? 'border-rose-300' : 'border-slate-300' }} py-2 px-3 text-sm focus:border-blue-600 focus:ring-blue-600">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_kategori }}"
                                    {{ old('id_kategori', $barang->id_kategori) == $category->id_kategori ? 'selected' : '' }}>
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
                        'value' => $barang->kode_barang,
                        'required' => true,
                        'helper' => 'Kode harus unik.',
                        'error' => $errors->first('kode_barang'),
                    ])

                    @include('components.input', [
                        'label' => 'Nama Barang',
                        'name' => 'nama_barang',
                        'value' => $barang->nama_barang,
                        'required' => true,
                        'error' => $errors->first('nama_barang'),
                    ])

                    @include('components.input', [
                        'label' => 'Stok Saat Ini',
                        'name' => 'stok',
                        'type' => 'number',
                        'value' => $barang->stok,
                        'required' => true,
                        'error' => $errors->first('stok'),
                    ])

                    @include('components.input', [
                        'label' => 'Stok Minimum',
                        'name' => 'stok_minimum',
                        'type' => 'number',
                        'value' => $barang->stok_minimum,
                        'required' => true,
                        'error' => $errors->first('stok_minimum'),
                    ])
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('barang.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
                    @include('components.button', [
                        'label' => 'Perbarui',
                        'type' => 'submit',
                    ])
                </div>
            </form>
        </div>
    </div>
@endsection

