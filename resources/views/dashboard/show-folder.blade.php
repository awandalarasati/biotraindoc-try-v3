<?php

?>
@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">

    {{-- Judul Folder + Badge Kode TNA + Link Ikon --}}
    <h2 style="color: #029dbb; font-size: 26px;">
        {{ $folder->title }}
        @if($folder->tna_code)
            <span style="
                background-color: #029dbb;
                color: white;
                font-size: 14px;
                font-weight: bold;
                padding: 3px 8px;
                border-radius: 12px;
                margin-left: 8px;
                display: inline-block;
                vertical-align: middle;
                position: relative;
                top: -1px;">
                {{ $folder->tna_code }}
            </span>
        @endif

        {{-- Tampilkan icon rantai (link) di sebelah badge kode TNA --}}
        @if($folder->description && filter_var($folder->description, FILTER_VALIDATE_URL))
            <a href="{{ $folder->description }}"
               target="_blank"
               title="Klik untuk buka link dokumentasi"
               style="margin-left: 5px; text-decoration: none; font-size: 20px; color: #029dbb;">
                🔗
            </a>
        @endif
    </h2>

    @if($folder->description && !filter_var($folder->description, FILTER_VALIDATE_URL))
        <p style="text-align: justify;">{{ $folder->description }}</p>
    @endif

    {{-- Search bar dan Jenis File --}}
    <div style="margin: 20px 0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
        <form method="GET" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <input type="text" name="search" placeholder="Cari"
                value="{{ request('search') }}"
                style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; flex: 1; min-width: 150px;">

            {{-- Dropdown filter Jenis File --}}
            <select name="jenis_file" onchange="this.form.submit()"
                style="padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                <option value="">Semua Jenis File</option>
                @foreach($jenisFiles as $jenis)
                    <option value="{{ $jenis }}" {{ request('jenis_file') == $jenis ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div style="background: white; border-radius: 15px; padding: 20px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 15px; min-width: 900px;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th style="text-align: center; padding: 12px; width: 20%;">Judul File</th>
                    <th style="text-align: center; padding: 12px; width: 30%;">Deskripsi File</th>
                    <th style="text-align: center; padding: 12px; width: 15%;">Jenis File</th>
                    <th style="text-align: center; padding: 12px; width: 15%;">File Size</th>
                    <th style="text-align: center; padding: 12px; width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($folder->documents as $document)
                    @php
                        $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                        $customIcons = [
                            'pdf' => 'pdf.png',
                            'docx' => 'word.png',
                            'xlsx' => 'excel.png',
                            'png' => 'png.png',
                            'jpg' => 'jpg.png',
                            'jpeg' => 'jpeg.png',
                            'mp4' => 'mp4.png',
                            'zip' => 'zip.png',
                            'mp3' => 'mp3.png'
                        ];
                        $icon = asset('assets/icons/' . ($customIcons[$extension] ?? 'icon_default.png'));
                    @endphp
                    <tr style="border-bottom: 1px solid #e6e6e6;">
                        {{-- Judul File --}}
                        <td style="padding: 12px; display: flex; align-items: center; gap: 10px;">
                            <img src="{{ $icon }}" alt="{{ $extension }} icon" width="32">
                            {{ $document->title }}
                        </td>

                        {{-- Deskripsi File dengan Logic Center/Justify --}}
                        <td style="padding: 12px; word-wrap: break-word; hyphens: auto;
                                   text-align: {{ strlen($document->description ?? '') <= 50 ? 'center' : 'justify' }};">
                            @if($document->description)
                                {{ $document->description }}
                            @endif
                        </td>

                        {{-- Jenis File --}}
                        <td style="text-align: center;">
                            @if($document->jenis_file == 'SPRL')
                                <span style="background:#4CAF50; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">SPRL</span>
                            @elseif($document->jenis_file == 'Dokumentasi')
                                <span style="background:#2196F3; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">Dokumentasi</span>
                            @elseif($document->jenis_file == 'Daftar Hadir')
                                <span style="background:#FF9800; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">Daftar Hadir</span>
                            @else
                                <span style="background:#9E9E9E; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">{{ $document->jenis_file }}</span>
                            @endif
                        </td>

                        {{-- File Size --}}
                        <td style="text-align: center;">
                            @php
                                $fileSizeMB = $document->file_size / 1048576;
                            @endphp
                            {{ number_format($fileSizeMB, 2) }} MB
                        </td>

                        {{-- Aksi --}}
                        <td style="text-align: center; white-space: nowrap;">
                            <a href="{{ route('documents.preview', $document->id) }}" style="color: #029dbb; margin-right: 6px;">🔍</a>
                            <a href="{{ route('documents.edit', $document->id) }}" style="color: orange; margin-right: 6px;">✏️</a>
                            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display: inline;" class="delete-form" data-filename="{{ $document->title }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn" style="border: none; background: transparent; color: red; cursor: pointer;">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #aaa;">Belum ada file di folder ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Dialog Konfirmasi Delete --}}
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Anda yakin untuk menghapus file ini?</p>
            <p class="filename-display"></p>
        </div>
        <div class="modal-footer">
            <button type="button" id="cancelBtn" class="btn-cancel">Tidak</button>
            <button type="button" id="confirmBtn" class="btn-confirm">Ya</button>
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
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease-out;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 0;
        min-width: 400px;
        max-width: 500px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
        overflow: hidden;
    }

    .modal-header {
        background-color: #f8f9fa;
        padding: 20px 24px;
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
        color: #666;
        font-size: 16px;
        line-height: 1.5;
    }

    .filename-display {
        font-weight: 600;
        color: #333 !important;
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        margin-top: 16px !important;
    }

    .modal-footer {
        padding: 20px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .btn-cancel, .btn-confirm {
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 80px;
    }

    .btn-cancel {
        background-color: #dc3545;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #c82333;
        transform: translateY(-1px);
    }

    .btn-confirm {
        background-color: #007bff;
        color: white;
    }

    .btn-confirm:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @media (max-width: 768px) {
        .modal-content {
            min-width: 300px;
            margin: 20px;
        }
        
        .modal-header, .modal-body, .modal-footer {
            padding: 16px 20px;
        }
        
        .btn-cancel, .btn-confirm {
            padding: 12px 20px;
            font-size: 16px;
        }
    }

    @media (max-width: 768px) {
        div[style*="padding: 40px;"] { padding: 20px !important; }
        table { min-width: 100%; }
        th, td { font-size: 13px !important; padding: 8px !important; }
        form[method="GET"] { flex-direction: column; }
        form[method="GET"] input, form[method="GET"] select { width: 100% !important; }
        h2 { font-size: 20px !important; }
        
        td[style*="text-align: justify"] {
            text-align: left !important;
            hyphens: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const filenameDisplay = document.querySelector('.filename-display');
    let currentForm = null;

    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentForm = this.closest('.delete-form');
            const filename = currentForm.getAttribute('data-filename');

            filenameDisplay.textContent = filename;
 
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    cancelBtn.addEventListener('click', function() {
        hideModal();
    });

    confirmBtn.addEventListener('click', function() {
        if (currentForm) {
            currentForm.submit();
        }
        hideModal();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            hideModal();
        }
    });

    function hideModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        currentForm = null;
    }
});
</script>
@endsection