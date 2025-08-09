@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Edit Folder
        </h3>

        <form id="edit-folder-form" action="{{ route('folders.update', $folder->id) }}" method="POST" style="margin-top: 30px;">
            @csrf
            @method('PUT')

            {{-- Judul Folder --}}
            <div style="margin-bottom: 20px;">
                <label for="title" style="font-weight: bold; color: #029dbb;">Judul Folder</label>
                <input type="text" id="title" name="title" value="{{ $folder->title }}" required
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            {{-- Kode TNA --}}
            <div style="margin-bottom: 20px;">
                <label for="tna_code" style="font-weight: bold; color: #029dbb;">Kode TNA</label>
                <input type="text" id="tna_code" name="tna_code" value="{{ $folder->tna_code }}"
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            {{-- Deskripsi --}}
            <div style="margin-bottom: 20px;">
                <label for="description" style="font-weight: bold; color: #029dbb;">Deskripsi / Link Dokumentasi</label>
                <textarea id="description" name="description" rows="4"
                          style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">{{ $folder->description }}</textarea>
            </div>

            <button type="button" onclick="openEditModal()"
                    style="padding: 12px 30px; background-color: #029dbb; color: white; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;">
                Simpan
            </button>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI EDIT --}}
<div id="editModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Edit</h3>
        </div>
        <div class="modal-body">
            <p>Anda yakin untuk mengedit folder ini?</p>
            <p class="filename-display">{{ $folder->title }}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditModal()">Tidak</button>
            <button type="button" class="btn-confirm" onclick="submitEditFolder()">Ya</button>
        </div>
    </div>
</div>

{{-- Style modal sama dengan edit.blade.php --}}
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
function submitEditFolder() {
    document.getElementById('edit-folder-form').submit();
}
</script>
@endsection
