<?php

?>
@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div class="card-box">
        <h3 class="heading-sub">Folder Kegiatan</h3>

        <!-- Search bar -->
        <div style="margin-top: 20px; margin-bottom: 20px;">
            <form method="GET" style="display: flex; max-width: 300px;">
                <input type="text" name="search" placeholder="Cari Judul / Kode TNA" value="{{ request('search') }}"
                       style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; width: 100%;">
            </form>
        </div>

        <div class="table-responsive">
            <table class="folder-table">
                <thead>
                    <tr class="row-sep-none">
                        <th>Judul Folder</th>
                        <th>Deskripsi / Link</th>
                        <th>Folder Size</th>
                        <th>Tanggal Unggah</th>
                        <th>Perubahan Terakhir</th>
                        <th>Kode TNA</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($folders as $folder)
                        <tr>
                            <td>
                                <a href="{{ route('folders.show', $folder->id) }}" class="folder-link">
                                    <img src="{{ asset('assets/icons/folder.png') }}" alt="Folder Icon" width="24">
                                    {{ $folder->title }}
                                </a>
                            </td>

                            <!-- Deskripsi atau Link -->
                            <td class="desc-cell
                                {{ ($folder->description && filter_var($folder->description, FILTER_VALIDATE_URL)) ? 'only-icon' : '' }}
                                {{ (strlen($folder->description) > 50) ? 'long-text' : '' }}">
                                @if($folder->description && filter_var($folder->description, FILTER_VALIDATE_URL))
                                    <a href="{{ $folder->description }}" target="_blank" title="Klik untuk buka link" class="link-icon" aria-label="Buka tautan">🔗</a>
                                @else
                                    {{ $folder->description ?? '-' }}
                                @endif
                            </td>

                            <td class="center-text">{{ $folder->folder_size ?? '0' }}</td>
                            <td class="center-text">{{ \Carbon\Carbon::parse($folder->created_at)->format('d M Y H:i:s') }}</td>
                            <td class="center-text">
                                {{ $folder->updated_at ? \Carbon\Carbon::parse($folder->updated_at)->format('d M Y H:i:s') : '-' }}
                            </td>
                            <td class="center-text">{{ $folder->tna_code ?? '-' }}</td>
                            <td class="center-text nowrap">
                                <a href="#" class="btn-edit-folder" data-id="{{ $folder->id }}" style="margin-right: 10px; color: orange;">✏️</a>
                                <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" style="display: inline;" class="delete-folder-form" data-foldername="{{ $folder->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-folder-btn" style="border: none; background: transparent; color: red; cursor: pointer;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="row-sep-none">
                            <td colspan="7" class="center-text" style="padding: 30px; color: #aaa;">Belum ada folder.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Dialog Konfirmasi Delete Folder --}}
<div id="deleteFolderModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Anda yakin untuk menghapus folder ini?</p>
            <p class="foldername-display"></p>
        </div>
        <div class="modal-footer">
            <button type="button" id="cancelFolderBtn" class="btn-cancel">Tidak</button>
            <button type="button" id="confirmFolderBtn" class="btn-confirm">Ya</button>
        </div>
    </div>
</div>

<style>
    .page-wrapper { padding: 40px; overflow-x: hidden; }
    .card-box { background: white; border-radius: 15px; padding: 20px; margin-top: 20px; }
    .heading-sub { color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px; }
    .table-responsive { width: 100%; overflow-x: auto; }

    /* pakai border-separate + separator di <tr> agar garis rapi di semua zoom */
    .folder-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 16px;
        min-width: 700px;
    }

    .folder-table th, .folder-table td {
        padding: 12px;
        vertical-align: middle;
        background: #fff;
    }
    .folder-table thead th {
        background-color: #f2f2f2;
        text-align: center;
        border-bottom: 1px solid #e6e6e6;
    }

    /* separator baris: satu garis untuk seluruh baris */
    .folder-table tbody tr {
        position: relative;
    }
    .folder-table tbody tr:not(:last-child)::after {
        content: "";
        position: absolute;
        left: 0; right: 0; bottom: 0;
        height: 1px;
        background: #e6e6e6;
        pointer-events: none;
    }
    /* baris tanpa separator (mis. baris kosong) */
    .folder-table .row-sep-none::after { display: none; }

    .folder-link { text-decoration: none; color: #029dbb; display: flex; align-items: center; gap: 10px; }
    .center-text { text-align: center; }

    .desc-cell{
        display:flex;
        align-items:center;
        justify-content:center;
        word-break: break-word;
        max-width: 250px;
        line-height: 1.5;
        min-height: 40px;
        text-align:center;
        padding: 8px; /* boleh beda, separator tetap konsisten karena ada di <tr> */
    }
    .desc-cell.long-text{ justify-content:flex-start; text-align:justify; }
    .desc-cell.only-icon{ justify-content:center; text-align:center; }

    .nowrap { white-space: nowrap; }

    /* Hilangkan underline & baseline shift pada ikon link */
    .desc-cell a,
    .desc-cell a:visited,
    .desc-cell a:hover,
    .desc-cell a:active{
        text-decoration: none !important;
        -webkit-text-decoration: none !important;
    }
    .link-icon{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px; height: 24px;
        font-size: 20px;
        line-height: 1;
        color: #029dbb;
    }
    .link-icon:hover{ color:#005f75; }

    /* Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5); z-index: 1000;
        display: flex; justify-content: center; align-items: center;
        animation: fadeIn 0.3s ease-out;
    }
    .modal-content {
        background: white; border-radius: 12px; padding: 0;
        min-width: 400px; max-width: 500px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out; overflow: hidden;
    }
    .modal-header { background-color:#f8f9fa; padding:20px 24px; border-bottom:1px solid #e9ecef; }
    .modal-header h3 { margin:0; font-size:18px; font-weight:600; color:#333; }
    .modal-body { padding:24px; text-align:center; }
    .modal-body p { margin:0 0 12px 0; color:#666; font-size:16px; line-height:1.5; }
    .foldername-display { font-weight:600; color:#333 !important; background:#f8f9fa; padding:8px 12px; border-radius:6px; border:1px solid #e9ecef; margin-top:16px !important; }
    .modal-footer { padding:20px 24px; display:flex; justify-content:flex-end; gap:12px; background:#f8f9fa; border-top:1px solid #e9ecef; }
    .btn-cancel, .btn-confirm { padding:10px 24px; border:none; border-radius:6px; font-size:14px; font-weight:500; cursor:pointer; transition:all .2s ease; min-width:80px; }
    .btn-cancel { background:#dc3545; color:#fff; } .btn-cancel:hover{ background:#c82333; transform:translateY(-1px); }
    .btn-confirm { background:#007bff; color:#fff; } .btn-confirm:hover{ background:#0056b3; transform:translateY(-1px); }

    @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    @keyframes slideIn { from{opacity:0; transform:scale(.9) translateY(-20px);} to{opacity:1; transform:scale(1) translateY(0);} }

    @media (max-width: 768px) {
        .page-wrapper { padding: 20px; }
        .folder-table { font-size: 14px; }
        .folder-table th, .folder-table td { padding: 8px; }
        .modal-content { min-width: 300px; margin: 20px; }
        .modal-header, .modal-body, .modal-footer { padding: 16px 20px; }
        .btn-cancel, .btn-confirm { padding: 12px 20px; font-size: 16px; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteFolderModal');
    const cancelBtn = document.getElementById('cancelFolderBtn');
    const confirmBtn = document.getElementById('confirmFolderBtn');
    const foldernameDisplay = document.querySelector('.foldername-display');
    let currentForm = null;

    document.querySelectorAll('.delete-folder-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentForm = this.closest('.delete-folder-form');
            const foldername = currentForm.getAttribute('data-foldername');
            foldernameDisplay.textContent = foldername;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    cancelBtn.addEventListener('click', hideModal);
    confirmBtn.addEventListener('click', function() {
        if (currentForm) currentForm.submit();
        hideModal();
    });
    modal.addEventListener('click', function(e) { if (e.target === modal) hideModal(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && modal.style.display === 'flex') hideModal(); });

    function hideModal(){
        modal.style.display='none';
        document.body.style.overflow='auto';
        currentForm=null;
    }

    document.querySelectorAll('.btn-edit-folder').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const id=this.getAttribute('data-id');
            window.location.href="/folders/"+id+"/edit";
        });
    });
});
</script>
@endsection
