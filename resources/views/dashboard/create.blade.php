<?php
?>
@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Tambah File ke Folder: {{ $folder->title }}
        </h3>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" style="margin-top: 30px;">
            @csrf
            <input type="hidden" name="folder_id" value="{{ $folder->id }}">

            {{-- Judul File --}}
            <div style="margin-bottom: 20px;">
                <label for="judul" style="font-weight: bold; color: #029dbb;">Judul File</label>
                <input type="text" id="judul" name="title" required
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            {{-- Deskripsi File (tidak wajib diisi) --}}
            <div style="margin-bottom: 20px;">
                <label for="deskripsi" style="font-weight: bold; color: #029dbb;">Deskripsi File</label>
                <textarea id="deskripsi" name="description" rows="4"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;"></textarea>
            </div>

            {{-- Waktu Pelaksanaan --}}
            <div style="margin-bottom: 20px;">
                <label for="waktu_pelaksanaan" style="font-weight: bold; color: #029dbb;">Waktu Pelaksanaan</label>
                <input type="text" id="waktu_pelaksanaan" name="waktu_pelaksanaan" placeholder="Contoh: 23 Juni 2025"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                <p style="color: #666; font-size: 12px; margin-top: 5px;">
                    *Masukkan waktu pelaksanaan secara manual (opsional)
                </p>
            </div>

            {{-- Dokumen --}}
            <div style="margin-bottom: 20px;">
                <label for="document" style="font-weight: bold; color: #029dbb;">Dokumen</label>
                <input type="file" id="document" name="document" accept=".png,.jpg,.jpeg,.pdf,.mp4,.docx,.xlsx,.zip" required style="margin-top: 10px;">
                <p style="color: red; font-size: 13px; margin-top: 10px;">
                    *Format yang didukung: .png, .jpg, .jpeg, .pdf, .mp4, .docx, .xlsx, .zip
                </p>
            </div>

            {{-- Jenis File --}}
            <div style="margin-bottom: 20px;">
                <label for="jenis_file" style="font-weight: bold; color: #029dbb;">Jenis File</label>
                <select id="jenis_file" name="jenis_file" onchange="toggleCustomJenis()"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                    <option value="SPRL">SPRL</option>
                    <option value="Dokumentasi">Dokumentasi</option>
                    <option value="Daftar Hadir">Daftar Hadir</option>
                    <option value="custom">➕ Tambah Jenis Lainnya</option>
                </select>

                {{-- Input untuk custom jenis file --}}
                <input type="text" id="custom_jenis" name="custom_jenis" placeholder="Masukkan jenis file baru"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; margin-top: 10px; display: none;">
            </div>

            {{-- Tombol Simpan --}}
            <button type="submit"
                style="padding: 12px 30px; background-color: #029dbb; color: white; border: none; border-radius: 10px; font-weight: bold;">
                Simpan
            </button>
        </form>
    </div>
</div>

<script>
function toggleCustomJenis() {
    let dropdown = document.getElementById('jenis_file');
    let customInput = document.getElementById('custom_jenis');
    if (dropdown.value === 'custom') {
        customInput.style.display = 'block';
        customInput.name = 'jenis_file';
    } else {
        customInput.style.display = 'none';
        customInput.name = 'custom_jenis_hidden';
    }
}
</script>
@endsection