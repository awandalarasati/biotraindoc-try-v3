@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Tambah Folder
        </h3>

        <form action="{{ route('folders.store') }}" method="POST" style="margin-top: 30px;">
            @csrf

            {{-- Judul Folder --}}
            <div style="margin-bottom: 20px;">
                <label for="title" style="font-weight: bold; color: #029dbb;">Judul Folder</label>
                <input type="text" name="title" id="title" required
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            {{-- Kode TNA --}}
            <div style="margin-bottom: 20px;">
                <label for="tna_code" style="font-weight: bold; color: #029dbb;">Kode TNA</label>
                <input type="text" name="tna_code" id="tna_code"
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                @error('tna_code')
                    <div style="color: red; font-size: 14px; margin-top: 5px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

                {{-- Deskripsi / Link Dokumentasi --}}
            <div style="margin-bottom: 20px;">
                <label for="description" style="font-weight: bold; color: #029dbb;">Deskripsi / Link Dokumentasi</label>
                <textarea name="description" id="description" rows="4"
                          placeholder="Isi dengan teks atau link dokumentasi (opsional)"
                          style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;"></textarea>
            </div>

            <button type="submit"
                    style="padding: 12px 30px; background-color: #029dbb; color: white; border: none; border-radius: 10px; font-weight: bold;">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection
