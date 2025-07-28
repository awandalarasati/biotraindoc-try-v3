@extends('layouts.app')

@section('content')
<div style="padding: 40px; overflow-x: auto;">
    <h2 style="color: #029dbb; font-size: 26px;">Dashboard</h2>

    <div style="background: white; border-radius: 15px; padding: 20px; margin-top: 20px; overflow-x: auto;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Folder Kegiatan
        </h3>

    <!-- Filter Pencarian -->
    <div style="margin-top: 20px; margin-bottom: 20px;">
        <form method="GET" style="display: flex; max-width: 300px;">
            <input type="text" name="search" placeholder="Cari Judul / Kode TNA" value="{{ request('search') }}"
                style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; width: 100%;">
        </form>
    </div>

        <table style="width: 100%; margin-top: 20px; border-collapse: collapse; font-size: 16px; min-width: 900px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="text-align: left; padding: 12px;">Judul Folder</th>
                    <th style="text-align: center; padding: 12px;">Deskripsi</th>
                    <th style="text-align: center; padding: 12px;">Folder Size</th>
                    <th style="text-align: center; padding: 12px;">Tanggal Unggah</th>
                    <th style="text-align: center; padding: 12px;">Perubahan Terakhir</th>
                    <th style="text-align: center; padding: 12px;">Kode TNA</th>
                    <th style="text-align: center; padding: 12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($folders as $folder)
                    <tr style="border-bottom: 1px solid #e6e6e6;">
                        <td style="padding: 15px;">
                            <a href="{{ route('folders.show', $folder->id) }}" style="text-decoration: none; color: #029dbb; display: flex; align-items: center; gap: 10px;">
                                <img src="{{ asset('assets/icons/folder.png') }}" alt="Folder Icon" width="24">
                                {{ $folder->title }}
                            </a>
                        </td>
                        <td style="padding: 15px; word-break: break-word; max-width: 250px;">{{ $folder->description }}</td>
                        <td style="text-align: center;">{{ $folder->folder_size ?? '0' }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($folder->created_at)->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            {{ $folder->updated_at ? \Carbon\Carbon::parse($folder->updated_at)->format('d M Y') : '-' }}
                        </td>
                        <td style="text-align: center;">{{ $folder->tna_code ?? '-' }}</td>

                        <td style="text-align: center; white-space: nowrap;">
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
                        <td colspan="6" style="text-align: center; padding: 30px; color: #aaa;">Belum ada folder.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="confirmation-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
    <div style="background:white; padding:30px; border-radius:10px; text-align:center; width:90%; max-width:400px;">
        <p id="confirmation-message" style="font-size:18px; margin-bottom:20px;"></p>
        <button id="confirm-yes" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; margin-right:10px;">Ya</button>
        <button id="confirm-no" style="padding:10px 20px; background:red; color:white; border:none; border-radius:5px;">Tidak</button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hapus folder
        document.querySelectorAll('.btn-delete-folder').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                showModal("Anda yakin untuk menghapus folder ini?", function () {
                    document.getElementById('delete-form-' + id).submit();
                });
            });
        });

        // Edit folder
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

<style>
    @media (max-width: 768px) {
        div[style*="padding: 40px;"] {
            padding: 20px !important;
        }

        table {
            font-size: 14px !important;
            min-width: 100%;
        }

        th, td {
            padding: 8px !important;
        }

        h2 {
            font-size: 22px !important;
        }

        h3 {
            font-size: 18px !important;
        }

        td a {
            flex-direction: column;
            gap: 5px !important;
            align-items: flex-start !important;
        }

        td[style*="word-break: break-word;"] {
            max-width: 150px !important;
        }

        #confirmation-modal > div {
            width: 95% !important;
        }
    }
</style>
@endsection
