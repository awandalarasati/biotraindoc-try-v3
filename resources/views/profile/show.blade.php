@extends('layouts.app')

@section('content')

<style>
    html, body {
        margin: 0;
        padding: 0;
    }

    .background {
        min-height: 100vh;
        background: linear-gradient(to bottom, #b2f0ff, #ffffff);
        padding: 40px 20px;
        font-family: sans-serif;
    }

    .box-profil {
        background: white;
        border-radius: 15px;
        padding: 40px 30px;
        max-width: 1000px;
        margin: auto;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .alert-success {
        max-width: 500px;
        margin: 0 auto 20px;
        background-color: #d1fae5;
        color: #047857;
        border: 1px solid #10b981;
        padding: 10px 15px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        row-gap: 12px;
    }

    @media screen and (max-width: 600px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    .action-box {
        background: #fefefe;
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 700px;
        margin: 30px auto 0;
    }
</style>

<div class="background">
    <h2 style="color: #029dbb; font-size: 26px; text-align: center;">{{ $greeting }}</h2>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

<div class="box-profil">
    {{-- Tombol Logout --}}
    <form action="{{ route('logout') }}" method="POST" style="position: absolute; top: 20px; right: 20px;">
        @csrf
        <button type="submit"
            style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <img src="{{ asset('assets/icons/logout.png') }}" alt="Logout" style="width: 20px; height: 20px;">
            <span style="color: red; font-weight: bold; font-size: 16px;">Logout</span>
        </button>
    </form>

    {{-- Foto dan Nama --}}
    <div style="text-align: center;">
        @if ($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profil"
                style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
        @else
            <img src="{{ asset('assets/icons/user.png') }}" alt="Foto Default"
                style="width: 100px; height: 100px; border-radius: 50%; background: #eee;">
        @endif

        <h4 style="color: #0285c7; font-weight: bold; font-size: 20px; margin-top: 10px;">
            {{ $user->name }}
        </h4>
    </div>

    {{-- Container untuk menyamakan panjang --}}
    <div style="max-width: 700px; margin: 30px auto 0; display: flex; flex-direction: column; gap: 25px;">
        
        {{-- Informasi Akun --}}
        <div style="background: #fefefe; border-radius: 12px; box-shadow: 0 3px 8px rgba(0,0,0,0.05); padding: 25px;">
            <h4 style="color: #029dbb; font-size: 18px; font-weight: bold; margin-bottom: 20px;">Informasi Akun</h4>

            <div class="info-grid">
                <div style="color: #555;">Nama</div>
                <div>{{ $user->name }}</div>

                <div style="color: #555;">Username</div>
                <div style="font-weight: bold;">{{ $user->username }}</div>

                <div style="color: #555;">Email</div>
                <div style="word-break: break-word;">{{ $user->email }}</div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div style="background: #fefefe; border-radius: 12px; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); padding: 25px;">
            <h4 style="text-align: center; color: #444; margin-bottom: 25px;">Akun</h4>

            <a href="{{ route('profile.edit.name') }}"
                style="display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; border: 1px solid #ccc; border-radius: 10px; text-decoration: none; margin-bottom: 12px;">
                <span style="display: flex; align-items: center; gap: 10px; color: #0098b0">
                    <img src="{{ asset('assets/icons/userijo.png') }}" alt="Ubah nama akun"
                        style="width: 20px; height: 20px;">Ubah nama akun
                </span>
                <span style="font-weight: bold; color: #0098b0">›</span>
            </a>

            <a href="{{ route('profile.edit.password') }}"
                style="display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; border: 1px solid #ccc; border-radius: 10px; text-decoration: none; margin-bottom: 12px;">
                <span style="display: flex; align-items: center; gap: 10px; color: #0098b0">
                    <img src="{{ asset('assets/icons/key.png') }}" alt="Ubah kata sandi akun"
                        style="width: 20px; height: 20px;">Ubah kata sandi akun
                </span>
                <span style="font-weight: bold; color: #0098b0">›</span>
            </a>

            <a href="{{ route('profile.edit.photo') }}"
                style="display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; border: 1px solid #ccc; border-radius: 10px; text-decoration: none;">
                <span style="display: flex; align-items: center; gap: 10px; color: #0098b0">
                    <img src="{{ asset('assets/icons/userprofile.png') }}" alt="Ubah foto profil akun"
                        style="width: 20px; height: 20px;">Ubah foto profil akun
                </span>
                <span style="font-weight: bold; color: #0098b0">›</span>
            </a>
        </div>
    </div>
</div>

</div>
@endsection
