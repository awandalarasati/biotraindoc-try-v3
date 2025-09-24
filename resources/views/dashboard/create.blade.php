@extends('layouts.app')

@section('content')
<div style="padding:40px; font-family:sans-serif;">
    <h2 style="color:#029dbb; font-size:26px;">{{ $greeting }}</h2>

    <div style="background:#fff; border-radius:15px; padding:40px; margin-top:20px;">
        <h3 style="color:#029dbb; font-size:22px; border-bottom:2px solid #029dbb; padding-bottom:10px;">
            Tambah File ke Folder: {{ $folder->title }}
        </h3>

        @if (session('error'))
            <div style="background:#fee2e2;color:#b91c1c;padding:10px;border-radius:6px;margin-top:16px;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background:#fff3cd;color:#8a6d3b;padding:10px;border-radius:6px;margin-top:16px;">
                <ul style="margin:0 0 0 16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" style="margin-top:24px;">
            @csrf
            <input type="hidden" name="folder_id" value="{{ $folder->id }}">
            <input type="hidden" id="custom_jenis_hidden" name="custom_jenis_hidden" value="{{ old('custom_jenis_hidden') }}">

            <div style="margin-bottom:16px;">
                <label for="judul" style="font-weight:bold; color:#029dbb;">Judul File</label>
                <input type="text" id="judul" name="title" value="{{ old('title') }}" required
                       style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:16px;">
                <label for="deskripsi" style="font-weight:bold; color:#029dbb;">Deskripsi File</label>
                <textarea id="deskripsi" name="description" rows="4"
                          style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label for="waktu_pelaksanaan" style="font-weight:bold; color:#029dbb;">Waktu Pelaksanaan</label>
                <input type="text" id="waktu_pelaksanaan" name="waktu_pelaksanaan"
                       value="{{ old('waktu_pelaksanaan') }}" placeholder="Contoh: 23 Juni 2025"
                       style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc;">
                <p style="color:#666; font-size:12px; margin-top:5px;">*Opsional</p>
            </div>

            <div style="margin-bottom:16px;">
                <label for="document" style="font-weight:bold; color:#029dbb;">Dokumen</label>
                <input type="file" id="document" name="document"
                       accept=".png,.jpg,.jpeg,.pdf,.mp4,.docx,.xlsx,.zip" required
                       style="margin-top:10px;">
                <p style="color:#ef4444; font-size:12px; margin-top:6px;">
                    Format yang didukung: .png, .jpg, .jpeg, .pdf, .mp4, .docx, .xlsx, .zip
                </p>
            </div>

            <div style="margin-bottom:16px;">
                <label for="jenis_file" style="font-weight:bold; color:#029dbb;">Jenis File</label>
                <select id="jenis_file" name="jenis_file" onchange="toggleCustomJenis()"
                        style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc;">
                    @php $oldJenis = old('jenis_file', 'SPRL'); @endphp
                    <option value="SPRL" {{ $oldJenis==='SPRL' ? 'selected' : '' }}>SPRL</option>
                    <option value="Dokumentasi" {{ $oldJenis==='Dokumentasi' ? 'selected' : '' }}>Dokumentasi</option>
                    <option value="Daftar Hadir" {{ $oldJenis==='Daftar Hadir' ? 'selected' : '' }}>Daftar Hadir</option>
                    <option value="custom" {{ $oldJenis==='custom' ? 'selected' : '' }}>➕ Tambah Jenis Lainnya</option>
                </select>

                <input type="text" id="custom_jenis" name="custom_jenis" value="{{ old('custom_jenis') }}"
                       placeholder="Masukkan jenis file baru"
                       style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc; margin-top:10px; display:none;">
            </div>

            <button type="submit"
                    style="padding:12px 30px; background-color:#029dbb; color:#fff; border:none; border-radius:10px; font-weight:bold;">
                Simpan
            </button>
        </form>
    </div>
</div>

<script>
function toggleCustomJenis(){
    const dd = document.getElementById('jenis_file');
    const ci = document.getElementById('custom_jenis');
    ci.style.display = (dd.value === 'custom') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function(){
    const ci = document.getElementById('custom_jenis');
    const hi = document.getElementById('custom_jenis_hidden');
    toggleCustomJenis();
    const syncHidden = () => { hi.value = ci.value; };
    ci.addEventListener('input', syncHidden);
    syncHidden();
});
</script>
@endsection
