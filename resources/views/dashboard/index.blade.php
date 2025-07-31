@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <h2 class="heading-main">Dashboard</h2>

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
                    <tr>
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

                            <!-- Perubahan: gabungan deskripsi atau link -->
                            <td class="desc-cell">
                                @if($folder->description && filter_var($folder->description, FILTER_VALIDATE_URL))
                                    <a href="{{ $folder->description }}" target="_blank" title="Klik untuk buka link" class="link-icon">🔗</a>
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
                                <form id="delete-form-{{ $folder->id }}" action="{{ route('folders.destroy', $folder->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-delete-folder" data-id="{{ $folder->id }}" style="border: none; background: transparent; color: red; cursor: pointer;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="center-text" style="padding: 30px; color: #aaa;">Belum ada folder.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="confirmation-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
    <div style="background:white; padding:30px; border-radius:10px; text-align:center; width:90%; max-width:400px;">
        <p id="confirmation-message" style="font-size:18px; margin-bottom:20px;"></p>
        <button id="confirm-yes" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; margin-right:10px;">Ya</button>
        <button id="confirm-no" style="padding:10px 20px; background:red; color:white; border:none; border-radius:5px;">Tidak</button>
    </div>
</div>

<!-- JS -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.btn-delete-folder').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                showModal("Anda yakin untuk menghapus folder ini?", function () {
                    document.getElementById('delete-form-' + id).submit();
                });
            });
        });

        document.querySelectorAll('.btn-edit-folder').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                window.location.href = "/folders/" + id + "/edit";
            });
        });
    });

    function showModal(message, onConfirm) {
        const modal = document.getElementById("confirmation-modal");
        document.getElementById("confirmation-message").innerText = message;
        modal.style.display = "flex";

        document.getElementById("confirm-yes").onclick = function () {
            modal.style.display = "none";
            onConfirm();
        };

        document.getElementById("confirm-no").onclick = function () {
            modal.style.display = "none";
        };
    }
</script>

<!-- CSS -->
<style>
    .page-wrapper { padding: 40px; overflow-x: hidden; }
    .card-box { background: white; border-radius: 15px; padding: 20px; margin-top: 20px; }
    .heading-main { color: #029dbb; font-size: 26px; }
    .heading-sub { color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px; }
    .table-responsive { width: 100%; overflow-x: auto; }
    .folder-table { width: 100%; border-collapse: collapse; font-size: 16px; min-width: 700px; }
    .folder-table th, .folder-table td { padding: 12px; border-bottom: 1px solid #e6e6e6; }
    .folder-table th { background-color: #f2f2f2; text-align: center; }
    .folder-link { text-decoration: none; color: #029dbb; display: flex; align-items: center; gap: 10px; }
    .center-text { text-align: center; }
    .desc-cell { word-break: break-word; max-width: 250px; text-align: justify; }
    .nowrap { white-space: nowrap; }
    .link-icon { font-size: 20px; text-decoration: none; color: #029dbb; }
    .link-icon:hover { color: #005f75; }
</style>
@endsection
