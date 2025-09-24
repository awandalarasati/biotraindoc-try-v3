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
    /* =============== Helpers =============== */

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

        $pub = public_path($rel);
        if (is_file($pub)) return $pub;

        $sto = storage_path('app/public/'.$rel);
        if (is_file($sto)) return $sto;

        return null;
    }

    /* =============== UI =============== */

    /**
     * /documents/create/{id?}
     * - ?folder_id=... : pakai folder itu
     * - {id} folder    : pakai folder itu
     * - {id} dokumen   : pakai folder milik dokumen
     * - tanpa apa-apa  : pakai folder terbaru (updated_at/created_at)
     */
    public function create(Request $request, $id = null)
    {
        // 1) Prioritas: query string ?folder_id=XX jika ada
        if ($request->filled('folder_id')) {
            $folder = Folder::find($request->query('folder_id'));
            if ($folder) {
                $greeting = $this->greet();
                return view('dashboard.create', compact('greeting', 'folder'));
            }
        }

        // 2) Coba {id} sebagai folder
        if ($id !== null) {
            $folder = Folder::find($id);
            if ($folder) {
                $greeting = $this->greet();
                return view('dashboard.create', compact('greeting', 'folder'));
            }

            // 3) Kalau bukan folder, coba {id} sebagai dokumen → ambil foldernya
            $doc = Document::find($id);
            if ($doc && $doc->folder) {
                $greeting = $this->greet();
                $folder = $doc->folder;
                return view('dashboard.create', compact('greeting', 'folder'));
            }
        }

        // 4) Fallback: ambil folder terbaru (tanpa filter user_id)
        $folder = Folder::orderByDesc('updated_at')
                        ->orderByDesc('created_at')
                        ->first();

        if (!$folder) {
            return redirect()->route('dashboard')->with('error', 'Buat folder terlebih dahulu.');
        }

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
            'document'           => 'required|file|max:204800', // 200 MB
            'jenis_file'         => 'required|string|max:255',
        ]);

        $file = $request->file('document');

        $uploadDir = public_path('uploads');
        try {
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); } else { @chmod($uploadDir, 0777); }
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal membuat folder upload: '.$e->getMessage())->withInput();
        }

        try {
            $ext          = strtolower($file->getClientOriginalExtension());
            $size         = $file->getSize();
            $originalName = $file->getClientOriginalName();

            $safeName = Str::uuid()->toString().'.'.$ext;
            $file->move($uploadDir, $safeName);

            Document::create([
                'folder_id'         => $request->folder_id,
                'title'             => $request->title,
                'description'       => $request->description,
                'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
                'file_path'         => 'uploads/'.$safeName,
                'file_type'         => $ext,
                'file_size'         => $size,
                'original_name'     => $originalName,
                'jenis_file'        => $request->jenis_file,
            ]);

            Folder::find($request->folder_id)?->touch();

            return redirect()->route('folders.show', $request->folder_id)
                             ->with('success', 'File berhasil diunggah.');
        } catch (Throwable $e) {
            return back()->with('error', 'Upload gagal: '.$e->getMessage())->withInput();
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
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); } else { @chmod($uploadDir, 0777); }

            $old = $this->resolveExistingPath($document);
            if ($old) @File::delete($old);

            $file         = $request->file('file');
            $ext          = strtolower($file->getClientOriginalExtension());
            $size         = $file->getSize();
            $originalName = $file->getClientOriginalName();

            $safeName = Str::uuid()->toString().'.'.$ext;
            $file->move($uploadDir, $safeName);

            $data['file_path']     = 'uploads/'.$safeName;
            $data['file_type']     = $ext;
            $data['file_size']     = $size;
            $data['original_name'] = $originalName;
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

    /* =============== Preview =============== */

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

    /* =============== RAW (embed) =============== */

    public function raw($id)
    {
        $document = Document::findOrFail($id);
        $path = $this->resolveExistingPath($document);
        abort_unless($path && is_file($path), 404, 'File tidak ditemukan.');

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf'        => 'application/pdf',
            'png'        => 'image/png',
            'jpg','jpeg' => 'image/jpeg',
            'gif'        => 'image/gif',
            'webp'       => 'image/webp',
            'mp4'        => 'video/mp4',
            'webm'       => 'video/webm',
            default      => mime_content_type($path) ?: 'application/octet-stream',
        };

        $filename = $document->original_name ?: basename($path);

        return response()->file($path, [
            'Content-Type'            => $mime,
            'Content-Disposition'     => 'inline; filename="'.$filename.'"',
            'X-Frame-Options'         => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self'",
        ]);
    }


    /* =============== Download (streaming) =============== */

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $path = $this->resolveExistingPath($document);
        abort_unless($path && is_file($path), 404, 'File tidak ditemukan.');

        $ext   = pathinfo($path, PATHINFO_EXTENSION);
        $name  = str($document->title)->slug('-').'.'.$ext;
        $mime  = mime_content_type($path) ?: 'application/octet-stream';
        $size  = filesize($path) ?: null;

        return response()->streamDownload(function() use ($path) {
            $fp = fopen($path, 'rb');
            while (!feof($fp)) {
                echo fread($fp, 8192);
                flush();
            }
            fclose($fp);
        }, $name, array_filter([
            'Content-Type'   => $mime,
            'Content-Length' => $size,
        ]));
    }
}
