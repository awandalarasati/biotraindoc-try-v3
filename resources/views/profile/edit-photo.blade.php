@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Ubah foto profil akun
        </h3>

        <form action="{{ route('profile.update.photo') }}" method="POST" enctype="multipart/form-data" style="margin-top: 30px;">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="profile_photo" style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Foto Profil Baru
                </label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" required
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                @error('profile_photo')
                    <div style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit"
                style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                Simpan
            </button>

            <a href="{{ route('profile') }}"
                style="background: red; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin-left: 10px;">
                Kembali
            </a>
        </form>
    </div>
</div>
@endsection
