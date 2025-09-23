<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentController extends Controller
{
    public function create($folder_id)
    {
        $folder = Folder::findOrFail($folder_id);

        $hour = Carbon::now('Asia/Jakarta')->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Selamat pagi!',
            $hour >= 12 && $hour < 15 => 'Selamat siang!',
            $hour >= 15 && $hour < 18 => 'Selamat sore!',
            default => 'Selamat malam!',
        };

        return view('dashboard.create', compact('greeting', 'folder'));
    }

    public function store(Request $request)
    {
        // normalisasi jenis_file (pakai custom bila ada)
        $jenis = $request->input('jenis_file');
        if ($jenis === 'custom' || empty($jenis)) {
            $jenis = $request->input('custom_jenis') ?: $request->input('custom_jenis_hidden');
        }
        if ($jenis) {
            $request->merge(['jenis_file' => $jenis]);
        }

        $request->validate([
            'folder_id'          => 'required|exists:folders,id',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'waktu_pelaksanaan'  => 'nullable|string|max:255',
            'document'           => 'required|file|max:204800', // 200MB
            'jenis_file'         => 'required|string|max:255',
        ]);

        $file = $request->file('document');

        // pastikan folder public/uploads ada
        $uploadDir = public_path('uploads');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // beri nama unik
        $ext      = strtolower($file->getClientOriginalExtension());
        $safeName = Str::uuid()->toString().'.'.$ext;

        // pindahkan ke public/uploads
        $file->move($uploadDir, $safeName);

        $document = Document::create([
            'folder_id'         => $request->folder_id,
            'title'             => $request->title,
            'description'       => $request->description,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'file_path'         => 'uploads/'.$safeName,                 // RELATIF dari public
            'file_type'         => $ext,
            'file_size'         => $file->getSize(),
            'original_name'     => $file->getClientOriginalName(),
            'jenis_file'        => $request->jenis_file,
        ]);

        // sentuh folder biar updated_at naik
        $folder = Folder::find($request->folder_id);
        $folder?->touch();

        return redirect()->route('folders.show', $request->folder_id)
                         ->with('success', 'File berhasil diunggah.');
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $folder   = $document->folder;

        $hour = Carbon::now('Asia/Jakarta')->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Selamat pagi!',
            $hour >= 12 && $hour < 15 => 'Selamat siang!',
            $hour >= 15 && $hour < 18 => 'Selamat sore!',
            default => 'Selamat malam!',
        };

        return view('dashboard.edit', compact('document', 'folder', 'greeting'));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // normalisasi jenis_file
        $jenis = $request->input('jenis_file');
        if ($jenis === 'custom' || empty($jenis)) {
            $jenis = $request->input('custom_jenis') ?: $request->input('custom_jenis_hidden');
        }
        if ($jenis) {
            $request->merge(['jenis_file' => $jenis]);
        }

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
            // hapus file lama di public jika ada
            if ($document->file_path && File::exists(public_path($document->file_path))) {
                File::delete(public_path($document->file_path));
            }

            $file = $request->file('file');

            $uploadDir = public_path('uploads');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

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

        if ($document->file_path && File::exists(public_path($document->file_path))) {
            File::delete(public_path($document->file_path));
        }

        $document->delete();
        Folder::find($folder_id)?->touch();

        return redirect()->route('folders.show', $folder_id)
                         ->with('success', 'File berhasil dihapus.');
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);

        // Office/arsip tidak bisa di-embed → langsung download agar tetap "bisa dilihat"
        $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        if (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'csv', 'zip', 'rar'])) {
            $path = public_path($document->file_path);
            abort_unless(file_exists($path), 404, 'File tidak ditemukan.');
            $downloadName = str($document->title)->slug('-').'.'.$ext;
            return response()->download($path, $downloadName);
        }

        $hour = Carbon::now('Asia/Jakarta')->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Selamat pagi!',
            $hour >= 12 && $hour < 15 => 'Selamat siang!',
            $hour >= 15 && $hour < 18 => 'Selamat sore!',
            default => 'Selamat malam!',
        };

        return view('dashboard.preview', compact('document', 'greeting'));
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $path = public_path($document->file_path);
        abort_unless(file_exists($path), 404, 'File tidak ditemukan.');
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $downloadName = str($document->title)->slug('-').'.'.$ext;

        return response()->download($path, $downloadName);
    }
}
