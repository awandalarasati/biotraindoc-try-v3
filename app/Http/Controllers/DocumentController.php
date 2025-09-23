<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class DocumentController extends Controller
{
    private function greet(): string
    {
        $hour = Carbon::now('Asia/Jakarta')->format('H');
        return match (true) {
            $hour >= 5  && $hour < 12 => 'Selamat pagi!',
            $hour >= 12 && $hour < 15 => 'Selamat siang!',
            $hour >= 15 && $hour < 18 => 'Selamat sore!',
            default                    => 'Selamat malam!',
        };
    }

    private function resolveExistingPath(Document $document): ?string
    {
        $rel = ltrim($document->file_path, '/');

        $pub = public_path($rel);                         // public/uploads/xxx
        if (is_file($pub)) return $pub;

        $sto = storage_path('app/public/'.$rel);          // storage/app/public/uploads/xxx
        if (is_file($sto)) return $sto;

        return null;
    }

    public function create($folder_id)
    {
        $folder   = Folder::findOrFail($folder_id);
        $greeting = $this->greet();
        return view('dashboard.create', compact('greeting', 'folder'));
    }

    public function store(Request $request)
    {
        $jenis = $request->input('jenis_file');
        if ($jenis === 'custom' || empty($jenis)) {
            $jenis = $request->input('custom_jenis') ?: $request->input('custom_jenis_hidden');
        }
        if ($jenis) $request->merge(['jenis_file' => $jenis]);

        $request->validate([
            'folder_id'          => 'required|exists:folders,id',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'waktu_pelaksanaan'  => 'nullable|string|max:255',
            'document'           => 'required|file|max:204800', // 200MB
            'jenis_file'         => 'required|string|max:255',
        ]);

        $file = $request->file('document');
        $uploadDir = public_path('uploads');
        try {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            } else {
                @chmod($uploadDir, 0777);
            }
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal membuat folder upload: '.$e->getMessage());
        }

        try {
            $ext      = strtolower($file->getClientOriginalExtension());
            $safeName = Str::uuid()->toString().'.'.$ext;
            $file->move($uploadDir, $safeName);

            $document = Document::create([
                'folder_id'         => $request->folder_id,
                'title'             => $request->title,
                'description'       => $request->description,
                'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
                'file_path'         => 'uploads/'.$safeName,
                'file_type'         => $ext,
                'file_size'         => $file->getSize(),
                'original_name'     => $file->getClientOriginalName(),
                'jenis_file'        => $request->jenis_file,
            ]);

            Folder::find($request->folder_id)?->touch();
            return redirect()->route('folders.show', $request->folder_id)
                             ->with('success', 'File berhasil diunggah.');
        } catch (Throwable $e) {
            return back()->with('error', 'Upload gagal: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $folder   = $document->folder;
        $greeting = $this->greet();
        return view('dashboard.edit', compact('document', 'folder', 'greeting'));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $jenis = $request->input('jenis_file');
        if ($jenis === 'custom' || empty($jenis)) {
            $jenis = $request->input('custom_jenis') ?: $request->input('custom_jenis_hidden');
        }
        if ($jenis) $request->merge(['jenis_file' => $jenis]);

        $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'waktu_pelaksanaan'  => 'nullable|string|max:255',
            'file'               => 'nullable|file|max:204800',
            'jenis_file'         => 'required|string|max:255',
        ]);

        $data = [
            'title'             => $request->title,
            'description'       => $request->description,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'jenis_file'        => $request->jenis_file,
        ];

        if ($request->hasFile('file')) {
            $uploadDir = public_path('uploads');
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); else @chmod($uploadDir, 0777);
            if ($document->file_path && File::exists(public_path($document->file_path))) {
                @File::delete(public_path($document->file_path));
            }

            $file     = $request->file('file');
            $ext      = strtolower($file->getClientOriginalExtension());
            $safeName = Str::uuid()->toString().'.'.$ext;
            $file->move($uploadDir, $safeName);

            $data['file_path']     = 'uploads/'.$safeName;
            $data['file_type']     = $ext;
            $data['file_size']     = $file->getSize();
            $data['original_name'] = $file->getClientOriginalName();
        }

        $document->update($data);
        $document->folder?->touch();

        return redirect()->route('folders.show', $document->folder_id)
                         ->with('success', 'File berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $document  = Document::findOrFail($id);
        $folder_id = $document->folder_id;

        $path = $this->resolveExistingPath($document);
        if ($path) @File::delete($path);

        $document->delete();
        Folder::find($folder_id)?->touch();

        return redirect()->route('folders.show', $folder_id)
                         ->with('success', 'File berhasil dihapus.');
    }

    // ======= PREVIEW PAGE =======
    public function preview($id)
    {
        $document = Document::findOrFail($id);
        $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        if (in_array($ext, ['doc','docx','xls','xlsx','csv','zip','rar'])) {
            return $this->download($id);
        }

        $greeting = $this->greet();
        return view('dashboard.preview', compact('document', 'greeting'));
    }

    // ======= RAW FILE (untuk <img>, <iframe>, <video>) =======
    public function raw($id)
    {
        $document = Document::findOrFail($id);
        $path = $this->resolveExistingPath($document);
        abort_unless($path && is_file($path), 404, 'File tidak ditemukan.');

        $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'pdf'   => 'application/pdf',
            'png'   => 'image/png',
            'jpg','jpeg' => 'image/jpeg',
            'gif'   => 'image/gif',
            'webp'  => 'image/webp',
            'mp4'   => 'video/mp4',
            'webm'  => 'video/webm',
            default => mime_content_type($path) ?: 'application/octet-stream',
        };

        return Response::file($path, ['Content-Type' => $mime]);
    }

    // ======= DOWNLOAD (attachment) =======
    public function download($id)
    {
        $document = Document::findOrFail($id);
        $path = $this->resolveExistingPath($document);
        abort_unless($path && is_file($path), 404, 'File tidak ditemukan.');

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $name = str($document->title)->slug('-').'.'.$ext;

        return response()->download($path, $name);
    }
}
