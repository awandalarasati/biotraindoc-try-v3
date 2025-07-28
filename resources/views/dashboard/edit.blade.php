@extends('layouts.app')

@section('content')
<style>
    .container-edit {
        padding: 40px;
        font-family: sans-serif;
    }
    .greeting {
        color: #029dbb;
        font-size: 26px;
    }
    .edit-box {
        background: white;
        border-radius: 15px;
        padding: 40px;
        margin-top: 20px;
    }
    .edit-title {
        color: #029dbb;
        font-size: 22px;
        border-bottom: 2px solid #029dbb;
        padding-bottom: 10px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        font-weight: bold;
        color: #029dbb;
    }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
    .file-info {
        margin-top: 10px;
        color: #555;
    }
    .file-link {
        color: #029dbb;
    }
    .note {
        color: red;
        font-size: 13px;
        margin-top: 10px;
    }
    .btn-submit {
        padding: 12px 30px;
        background-color: #029dbb;
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
    }
    .text-right {
        text-align: right;
    }
    .hidden {
        display: none;
    }
</style>

<div class="container-edit">
    <h2 class="greeting">{{ $greeting }}</h2>

    <div class="edit-box">
        {{-- ✅ Judul pakai nama file --}}
        <h3 class="edit-title">{{ $document->title }}</h3>

        <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 30px;">
            @csrf
            @method('PUT')

            {{-- Judul File --}}
            <div class="form-group">
                <label for="judul" class="form-label">Judul File</label>
                <input type="text" id="judul" name="title" value="{{ old('title', $document->title) }}" required class="form-input">
            </div>

            {{-- Deskripsi File --}}
            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi File</label>
                <textarea id="deskripsi" name="description" rows="4" class="form-textarea">{{ old('description', $document->description) }}</textarea>
            </div>

            {{-- Dokumen --}}
            <div class="form-group">
                <label for="file" class="form-label">Dokumen</label>

                <p class="file-info">
                    <strong>File saat ini:</strong>
                    <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="file-link">
                        {{ $document->original_name ?? basename($document->file_path) }}
                    </a>
                </p>

                <input type="file" id="file" name="file" accept=".png,.jpg,.jpeg,.pdf,.mp4,.docx,.xlsx,.zip" class="form-input">
                <p class="note">
                    *Format yang didukung: .png, .jpg, .jpeg, .pdf, .mp4, .docx, .xlsx, .zip
                </p>
            </div>

            {{-- Jenis File --}}
            @php
                $isCustom = !in_array($document->jenis_file, ['SPRL', 'Dokumentasi', 'Daftar Hadir']);
            @endphp
            <div class="form-group">
                <label for="jenis_file" class="form-label">Jenis File</label>
                <select id="jenis_file" name="jenis_file" onchange="toggleCustomJenis()" class="form-select">
                    <option value="SPRL" {{ $document->jenis_file == 'SPRL' ? 'selected' : '' }}>SPRL</option>
                    <option value="Dokumentasi" {{ $document->jenis_file == 'Dokumentasi' ? 'selected' : '' }}>Dokumentasi</option>
                    <option value="Daftar Hadir" {{ $document->jenis_file == 'Daftar Hadir' ? 'selected' : '' }}>Daftar Hadir</option>
                    <option value="custom" {{ $isCustom ? 'selected' : '' }}>Tambah Jenis Lainnya</option>
                </select>

                {{-- Input custom jenis file --}}
                <input type="text" id="custom_jenis"
                       name="{{ $isCustom ? 'jenis_file' : 'custom_jenis_hidden' }}"
                       placeholder="Masukkan jenis file baru"
                       value="{{ $isCustom ? $document->jenis_file : '' }}"
                       class="form-input {{ $isCustom ? '' : 'hidden' }}"
                       style="margin-top: 10px;">
            </div>

            {{-- ✅ Tombol simpan rata kanan --}}
            <div class="text-right">
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
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
