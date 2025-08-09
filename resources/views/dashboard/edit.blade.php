@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">

    {{-- Ucapan Salam --}}
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    {{-- Kotak Edit File --}}
    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Edit File
        </h3>

        <form id="edit-file-form" action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 30px;">
            @csrf
            @method('PUT')

            {{-- Judul File --}}
            <div style="margin-bottom: 20px;">
                <label for="judul" style="font-weight: bold; color: #029dbb;">Judul File</label>
                <input type="text" id="judul" name="title" value="{{ old('title', $document->title) }}" required
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            {{-- Deskripsi File --}}
            <div style="margin-bottom: 20px;">
                <label for="deskripsi" style="font-weight: bold; color: #029dbb;">Deskripsi File</label>
                <textarea id="deskripsi" name="description" rows="4"
                          style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">{{ old('description', $document->description) }}</textarea>
            </div>

            {{-- File --}}
            <div style="margin-bottom: 20px;">
                <label for="file" style="font-weight: bold; color: #029dbb;">File</label>
                <p style="margin-top: 8px; color: #555;">
                    <strong>File saat ini:</strong>
                    <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" style="color: #029dbb;">
                        {{ $document->original_name ?? basename($document->file_path) }}
                    </a>
                </p>
                <input type="file" id="file" name="file" class="form-input"
                       accept=".png,.jpg,.jpeg,.pdf,.mp4,.docx,.xlsx,.zip"
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                <p style="color: red; font-size: 13px; margin-top: 10px;">
                    *Format yang didukung: .png, .jpg, .jpeg, .pdf, .mp4, .docx, .xlsx, .zip
                </p>
            </div>

            {{-- Jenis File --}}
            @php
                $isCustom = !in_array($document->jenis_file, ['SPRL', 'Dokumentasi', 'Daftar Hadir']);
            @endphp
            <div style="margin-bottom: 20px;">
                <label for="jenis_file" style="font-weight: bold; color: #029dbb;">Jenis File</label>
                <select id="jenis_file" name="jenis_file" onchange="toggleCustomJenis()"
                        style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
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
                       style="margin-top: 10px; width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; {{ $isCustom ? '' : 'display:none;' }}">
            </div>

            {{-- Tombol Simpan --}}
            <div style="text-align: left;">
                <button type="button" onclick="openEditModal()"
                        style="padding: 12px 30px; background-color: #029dbb; color: white; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- KONFIRMASI EDIT --}}
<div id="editModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Edit</h3>
        </div>
        <div class="modal-body">
            <p>Anda yakin untuk mengedit file ini?</p>
            <p class="filename-display">{{ $document->title }}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditModal()">Tidak</button>
            <button type="button" class="btn-confirm" onclick="submitEditFile()">Ya</button>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-content {
        background: white;
        border-radius: 12px;
        min-width: 380px;
        max-width: 450px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        overflow: hidden;
    }
    .modal-header {
        background-color: #f8f9fa;
        padding: 18px 24px;
        border-bottom: 1px solid #e9ecef;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    .modal-body {
        padding: 24px;
        text-align: center;
    }
    .modal-body p {
        margin: 0 0 12px 0;
        font-size: 16px;
        color: #555;
    }
    .filename-display {
        font-weight: 600;
        color: #333;
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        margin-top: 10px;
    }
    .modal-footer {
        background-color: #f8f9fa;
        padding: 18px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #e9ecef;
    }

    .btn-cancel, .btn-confirm {
        padding: 10px 22px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        min-width: 100px;
        text-align: center;
        font-size: 14px;
    }

    .btn-cancel {
        background: #dc3545;
        color: #fff;
    }

    .btn-cancel:hover {
        background: #c82333;
    }

    .btn-confirm {
        background: #007bff;
        color: #fff;
    }

    .btn-confirm:hover {
        background: #0056b3;
    }

</style>

<script>
function openEditModal() {
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
function submitEditFile() {
    document.getElementById('edit-file-form').submit();
}
</script>
@endsection
