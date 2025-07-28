@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">Tambah Folder</h2>

    <div style="background: white; border-radius: 15px; padding: 30px; margin-top: 20px;">
        <form action="{{ route('folders.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 5px;">Judul Folder</label>
                <input type="text" name="title" id="title" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="tna_code" style="display: block; margin-bottom: 5px;">Kode TNA</label>
                <input type="text" name="tna_code" id="tna_code"
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
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;"></textarea>
            </div>

            <button type="submit"
                    style="background: #029dbb; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection
