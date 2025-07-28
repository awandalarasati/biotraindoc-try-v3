@extends('layouts.app')

@section('content')
<div style="padding: 40px; font-family: sans-serif;">
    {{-- Judul Folder + Badge Kode TNA --}}
    <h2 style="color: #029dbb; font-size: 26px;">
        {{ $folder->title }}
        @if($folder->tna_code)
            <span style="
                background-color: #029dbb;
                color: white;
                font-size: 14px;
                font-weight: bold;
                padding: 3px 8px;
                border-radius: 12px;
                margin-left: 8px;
                display: inline-block;
                vertical-align: middle;
                position: relative;
                top: -1px;">
                {{ $folder->tna_code }}
            </span>
        @endif
    </h2>

    {{-- Deskripsi Folder --}}
    @if($folder->description)
        <p>{{ $folder->description }}</p>
    @endif

    {{-- 🔽 Filter Pencarian dan Jenis File --}}
    <div style="margin: 20px 0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
        <form method="GET" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <input type="text" name="search" placeholder="Cari"
                value="{{ request('search') }}"
                style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; flex: 1; min-width: 150px;">

            {{-- Dropdown filter Jenis File --}}
            <select name="jenis_file" onchange="this.form.submit()"
                style="padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                <option value="">Semua Jenis File</option>
                @foreach($jenisFiles as $jenis)
                    <option value="{{ $jenis }}" {{ request('jenis_file') == $jenis ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- 🔽 Daftar File --}}
    <div style="background: white; border-radius: 15px; padding: 20px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 15px; min-width: 900px;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th style="text-align: left; padding: 12px; width: 15%;">Judul File</th>
                    <th style="text-align: center; padding: 12px; width: 25%;">Deskripsi File</th>
                    <th style="text-align: center; padding: 12px; width: 10%;">Jenis File</th>
                    <th style="text-align: center; padding: 12px; width: 10%;">File Size</th>
                    <th style="text-align: center; padding: 12px; width: 12%;">Tanggal Unggah</th>
                    <th style="text-align: center; padding: 12px; width: 12%;">Perubahan Terakhir</th>
                    <th style="text-align: center; padding: 12px; width: 6%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($folder->documents as $document)
                    @php
                        $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                        $customIcons = [
                            'pdf' => 'pdf.png',
                            'docx' => 'word.png',
                            'xlsx' => 'excel.png',
                            'png' => 'png.png',
                            'jpg' => 'jpg.png',
                            'jpeg' => 'jpeg.png',
                            'mp4' => 'mp4.png',
                            'zip' => 'zip.png',
                        ];
                        $icon = asset('assets/icons/' . ($customIcons[$extension] ?? 'icon_default.png'));
                    @endphp
                    <tr style="border-bottom: 1px solid #e6e6e6;">
                        {{-- Judul File --}}
                        <td style="padding: 12px; display: flex; align-items: center; gap: 10px;">
                            <img src="{{ $icon }}" alt="{{ $extension }} icon" width="32">
                            {{ $document->title }}
                        </td>

                        {{-- Deskripsi File --}}
                        <td style="padding: 12px; text-align: justify; word-wrap: break-word; width: 25%; ">
                            @if($document->description)
                                {{ $document->description }}
                            @endif
                        </td>

                        {{-- Jenis File --}}
                        <td style="text-align: center;">
                            @if($document->jenis_file == 'SPRL')
                                <span style="background:#4CAF50; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">SPRL</span>
                            @elseif($document->jenis_file == 'Dokumentasi')
                                <span style="background:#2196F3; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">Dokumentasi</span>
                            @elseif($document->jenis_file == 'Daftar Hadir')
                                <span style="background:#FF9800; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">Daftar Hadir</span>
                            @else
                                <span style="background:#9E9E9E; color:white; padding:3px 8px; border-radius:8px; font-size:12px;">{{ $document->jenis_file }}</span>
                            @endif
                        </td>

                        {{-- File Size --}}
                        <td style="text-align: center;">
                            @php
                                $fileSizeMB = $document->file_size / 1048576;
                            @endphp
                            {{ number_format($fileSizeMB, 2) }} MB
                        </td>
                        {{-- Tanggal Upload --}}
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($document->created_at)->format('d M Y') }}</td>

                        {{-- Perubahan Terakhir --}}
                        <td style="text-align: center;">{{ $document->updated_at ? \Carbon\Carbon::parse($document->updated_at)->format('d M Y') : '-' }}</td>

                        {{-- Aksi --}}
                        <td style="text-align: center; white-space: nowrap;">
                            <a href="{{ route('documents.preview', $document->id) }}" style="color: #029dbb; margin-right: 6px;">🔍</a>
                            <a href="{{ route('documents.edit', $document->id) }}" style="color: orange; margin-right: 6px;">✏️</a>
                            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus file ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="border: none; background: transparent; color: red; cursor: pointer;">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #aaa;">Belum ada file di folder ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CSS Responsive --}}
<style>
    @media (max-width: 768px) {
        div[style*="padding: 40px;"] {
            padding: 20px !important;
        }

        table {
            min-width: 100%;
        }

        th, td {
            font-size: 13px !important;
            padding: 8px !important;
        }

        form[method="GET"] {
            flex-direction: column;
        }

        form[method="GET"] input,
        form[method="GET"] select {
            width: 100% !important;
        }

        h2 {
            font-size: 20px !important;
        }
    }
</style>
@endsection
