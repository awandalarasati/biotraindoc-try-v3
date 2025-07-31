@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb; font-size: 26px;">{{ $greeting }}</h2>

    <div style="background: white; border-radius: 15px; padding: 40px; margin-top: 20px;">
        <h3 style="color: #029dbb; font-size: 22px; border-bottom: 2px solid #029dbb; padding-bottom: 10px;">
            Kata sandi akun
        </h3>

        <form action="{{ route('profile.update.password') }}" method="POST" style="margin-top: 30px;">
            @csrf

            {{-- Kata sandi sebelumnya --}}
            <div style="margin-bottom: 20px;">
                <label for="current_password" style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Kata sandi sebelumnya
                </label>
                <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden;">
                    <input type="password" name="current_password" id="current_password" required
                        style="flex: 1; padding: 10px; border: none; min-width: 0;"
                        placeholder=" ">
                    <img src="{{ asset('assets/icons/eye.png') }}" id="eye_current_password"
                        onclick="togglePassword('current_password')" title="Lihat"
                        style="width: 20px; height: 20px; cursor: pointer; margin-right: 10px;">
                </div>
                @error('current_password')
                    <div style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kata sandi baru --}}
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Kata sandi baru
                </label>
                <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden;">
                    <input type="password" name="password" id="password" required minlength="8"
                        style="flex: 1; padding: 10px; border: none; min-width: 0;"
                        placeholder=" ">
                    <img src="{{ asset('assets/icons/eye.png') }}" id="eye_password"
                        onclick="togglePassword('password')" title="Lihat"
                        style="width: 20px; height: 20px; cursor: pointer; margin-right: 10px;">
                </div>
                @error('password')
                    <div style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Konfirmasi kata sandi baru --}}
            <div style="margin-bottom: 20px;">
                <label for="password_confirmation" style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Konfirmasi kata sandi baru
                </label>
                <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden;">
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                        style="flex: 1; padding: 10px; border: none; min-width: 0;"
                        placeholder=" ">
                    <img src="{{ asset('assets/icons/eye.png') }}" id="eye_password_confirmation"
                        onclick="togglePassword('password_confirmation')" title="Lihat"
                        style="width: 20px; height: 20px; cursor: pointer; margin-right: 10px;">
                </div>
            </div>


            {{-- Tombol Simpan & Kembali --}}
            <div class="form-buttons">
                <button type="submit" class="btn-save">Simpan</button>
                <a href="{{ route('profile') }}" class="btn-back">Kembali</a>
            </div>
        </form>
    </div>
</div>

<style>
    .form-buttons {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .btn-save,
    .btn-back {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: bold;
        color: white;
        border: none;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 100px;
        min-height: 40px;
    }

    .btn-save {
        background-color: #3b82f6;
    }

    .btn-back {
        background-color: red;
    }

    @media screen and (max-width: 375px) {
        .form-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-save,
        .btn-back {
            width: 100%;
        }
    }
</style>
{{-- JavaScript untuk toggle icon mata --}}
<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById('eye_' + fieldId);
        const isHidden = input.type === "password";

        input.type = isHidden ? "text" : "password";
        icon.src = isHidden
            ? "{{ asset('assets/icons/hidden.png') }}"
            : "{{ asset('assets/icons/eye.png') }}";
    }
</script>
@endsection
