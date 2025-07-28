@extends('layouts.app')

@section('content')
<style>
    .preview-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 900px;
        margin: 0 auto;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 26px;
        font-weight: bold;
        color: red;
        text-decoration: none;
    }

    iframe, video, img {
        width: 100%;
        border: none;
        border-radius: 10px;
        margin-top: 20px;
    }

    video {
        height: 400px;
    }

    img {
        max-height: 500px;
        object-fit: contain;
    }

    .info {
        margin-bottom: 20px;
    }

    .info strong {
        color: #006e9c;
    }

    .action-btns {
        display: flex;
        justify-content: end;
        margin-top: 20px;
        gap: 10px;
    }

    .action-btns a {
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: bold;
        color: white;
        background-color: #0284c7;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .action-btns a:hover {
        background-color: #0369a1;
    }
</style>

<div style="padding: 40px; font-family: sans-serif;">
    <h2 style="color: #029dbb;">{{ $greeting }}</h2>

    <div class="preview-container">
        <a href="{{ url()->previous() }}" class="close-btn">✕</a>

        <h3 style="color: #0284c7; font-size: 22px;">{{ $document->title }}</h3>

        <div class="info">
            <p><strong>Deskripsi File:</strong> {{ $document->description }}</p>
            <p><strong>Status:</strong> {{ ucfirst($document->status) }}</p>
        </div>

        @php
            $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
            $url = asset('storage/' . $document->file_path);
        @endphp

        @if (in_array($ext, ['pdf']))
            <iframe src="{{ $url }}" height="500px"></iframe>
        @elseif (in_array($ext, ['png', 'jpg', 'jpeg']))
            <img src="{{ $url }}" alt="Preview Gambar">
        @elseif (in_array($ext, ['mp4', 'webm']))
            <video controls>
                <source src="{{ $url }}" type="video/{{ $ext }}">
                Browser tidak mendukung pemutaran video.
            </video>
        @else
            <p style="text-align: center; margin-top: 30px;">
                File ini tidak dapat ditampilkan langsung di halaman. Silakan unduh untuk melihat.
            </p>
        @endif

        <div class="action-btns">
            <a href="{{ $url }}" download>Unduh</a>
            <a href="{{ $url }}" target="_blank">Buka di tab lain</a>
        </div>
    </div>
</div>
@endsection
