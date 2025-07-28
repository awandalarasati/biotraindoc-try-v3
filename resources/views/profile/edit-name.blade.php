@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Ubah nama akun
        </h3>

        <form action="{{ route('profile.update.name') }}" method="POST" style="margin-top: 30px;">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: bold; margin-bottom: 8px;">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                @error('name')
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
