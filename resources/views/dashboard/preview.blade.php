@extends('layouts.app')

@section('content')
<style>
    :root{
        --card-width: 1100px;
        --card-pad: 30px;
    }

    .page-wrap{ padding:40px; font-family:sans-serif;
    
    }
    .preview-shell{
        width: var(--card-width);
        max-width: 100%;
        margin: 0 auto;
        position: relative;
    }

    .greeting-row{
        padding-left: var(--card-pad);
        padding-right: var(--card-pad);
        margin-bottom: 10px;
    }
    .greeting-row h2{
        color:#029dbb;
        margin:0;
        font-size:28px;
        line-height:1.2;
    }

    .preview-container{
        background:#fff;
        border-radius:15px;
        padding: var(--card-pad);
        box-shadow: 0 3px 12px rgba(0,0,0,.1);
        position: relative;
    }

    .close-btn{
        position:absolute;
        top:16px;
        right:16px;
        font-size:26px;
        font-weight:700;
        color:#e11d48;
        text-decoration:none;
        line-height:1;
    }

    .file-head h3{
        color:#0284c7;
        font-size:24px;
        margin:0 0 6px 0;
    }
    .file-head .info strong{ color:#006e9c; }

    .separator-line{
        height:3px; background:#029dbb; border-radius:3px;
        margin:18px 0 22px 0; opacity:.6;
    }

    iframe,video,img{
        width:100%; border:none; border-radius:10px; display:block;
    }
    iframe{ height:600px; }
    video { height:500px; }
    img   { max-height:600px; object-fit:contain; }

    .action-btns{
        display:flex; justify-content:flex-end; gap:10px; margin-top:18px;
    }
    .action-btns a{
        text-decoration:none; padding:12px 20px; border-radius:8px;
        font-weight:700; color:#fff; background:#0284c7;
        box-shadow:0 2px 4px rgba(0,0,0,.1);
    }
    .action-btns a:hover{ background:#0369a1; }

    .badge{ padding:4px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#fff; display:inline-block; margin-left:8px; }
    .badge-sprl{background:#4CAF50;} .badge-dokumentasi{background:#2196F3;}
    .badge-daftarhadir{background:#FF9800;} .badge-default{background:#9E9E9E;}

    @media (max-width: 768px){
        .page-wrap{ padding:24px; }
        :root{ --card-width: 100%; --card-pad: 20px; }
        .greeting-row h2{ font-size:24px; }
        .file-head h3{ font-size:20px; }
        iframe{ height:480px; } video{ height:360px; } img{ max-height:480px; }
    }
</style>

<div class="page-wrap">
    <div class="preview-shell">
        <div class="greeting-row">
            <h2>{{ $greeting }}</h2>
        </div>

        <div class="preview-container">
            <a href="{{ url()->previous() }}" class="close-btn" aria-label="Tutup">✕</a>

            <div class="file-head">
                <h3>{{ $document->title }}</h3>
                <div class="info">
                    <p><strong>Deskripsi File:</strong> {{ $document->description }}</p>
                    <p>
                        <strong>Jenis File:</strong>
                        @if($document->jenis_file == 'SPRL')
                            <span class="badge badge-sprl">SPRL</span>
                        @elseif($document->jenis_file == 'Dokumentasi')
                            <span class="badge badge-dokumentasi">Dokumentasi</span>
                        @elseif($document->jenis_file == 'Daftar Hadir')
                            <span class="badge badge-daftarhadir">Daftar Hadir</span>
                        @else
                            <span class="badge badge-default">{{ $document->jenis_file }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="separator-line"></div>

            @php
                $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                $url = route('documents.raw', $document->id);
            @endphp


            @if (in_array($ext, ['pdf']))
                <iframe src="{{ $url }}"></iframe>
            @elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']))
                <img src="{{ $url }}" alt="Preview Gambar">
            @elseif (in_array($ext, ['mp4', 'webm']))
                <video controls>
                    <source src="{{ $url }}" type="video/{{ $ext }}">
                    Browser tidak mendukung pemutaran video.
                </video>
            @else
                <p style="text-align:center;margin-top:30px;font-size:16px;color:#666;">
                    File ini tidak dapat ditampilkan langsung di halaman. Silakan unduh untuk melihat.
                </p>
            @endif

            <div class="action-btns">
                <a href="{{ route('documents.download', $document->id) }}">Unduh</a>
                <a href="{{ $url }}" target="_blank" rel="noopener">Buka di tab lain</a>
            </div>
        </div>
    </div>
</div>
@endsection
