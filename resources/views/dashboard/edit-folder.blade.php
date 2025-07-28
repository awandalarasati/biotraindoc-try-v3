@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">Edit Folder</h2>

    <div style="background: white; border-radius: 15px; padding: 30px; margin-top: 20px;">
        <form id="edit-form" action="{{ route('folders.update', $folder->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 5px;">Judul Folder</label>
                <input type="text" name="title" id="title" value="{{ $folder->title }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="tna_code" style="display: block; margin-bottom: 5px;">Kode TNA</label>
                <input type="text" name="tna_code" id="tna_code" value="{{ $folder->tna_code }}"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                        @error('tna_code')
                            <div style="color: red; font-size: 14px; margin-top: 5px;">
                                {{ $message }}
                            </div>
                        @enderror
                                </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; margin-bottom: 5px;">Deskripsi Folder</label>
                <textarea name="description" id="description" rows="4"
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">{{ $folder->description }}</textarea>
            </div>

            <button type="button" onclick="confirmUpdate()"
                    style="background: #029dbb; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Simpan
            </button>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="confirmation-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
    <div style="background:white; padding:30px; border-radius:10px; text-align:center; width:90%; max-width:400px;">
        <p id="confirmation-message" style="font-size:18px; margin-bottom:20px;"></p>
        <button id="confirm-yes" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; margin-right:10px;">Ya</button>
        <button id="confirm-no" style="padding:10px 20px; background:red; color:white; border:none; border-radius:5px;">Tidak</button>
    </div>
</div>

<script>
    function confirmUpdate() {
        showModal("Anda yakin untuk mengedit folder ini?", function () {
            document.getElementById('edit-form').submit();
        });
    }

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
@endsection
