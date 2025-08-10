<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
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
        // ---- Normalisasi jenis_file dari dropdown/custom (jaga-jaga)
        $jenis = $request->input('jenis_file');
        if ($jenis === 'custom' || empty($jenis)) {
            // dari form create, JS sudah mengubah name jadi jenis_file.
            // kalau belum, ambil dari input cadangan.
            $jenis = $request->input('custom_jenis') ?: $request->input('custom_jenis_hidden');
        }
        if ($jenis) {
            $request->merge(['jenis_file' => $jenis]);
        }
        // -------------------------------------------------------------

        $request->validate([
            'folder_id'   => 'required|exists:folders,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'document'    => 'required|file|max:204800', // 200 MB
            'jenis_file'  => 'required|string|max:255',
        ]);

        $file = $request->file('document');
        $path = $file->store('uploads', 'public');

        $document = Document::create([
            'folder_id'     => $request->folder_id,
            'title'         => $request->title,
            'description'   => $request->description,
            'file_path'     => $path,
            'file_type'     => $file->getClientOriginalExtension(),
            'file_size'     => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
            'jenis_file'    => $request->jenis_file,
        ]);

        // update timestamp folder
        $folder = Folder::find($request->folder_id);
        $folder?->touch();

        return redirect()->route('folders.show', $request->folder_id)
                         ->with('success', 'File berhasil diunggah.');
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $folder = $document->folder;

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

        // ---- Normalisasi jenis_file dari dropdown/custom (penting untuk Edit)
        $jenis = $request->input('jenis_file');
        if ($jenis === 'custom' || empty($jenis)) {
            $jenis = $request->input('custom_jenis') ?: $request->input('custom_jenis_hidden');
        }
        if ($jenis) {
            $request->merge(['jenis_file' => $jenis]);
        }
        // ---------------------------------------------------------------

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|max:204800', // 200 MB
            'jenis_file'  => 'required|string|max:255',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'jenis_file'  => $request->jenis_file,
        ];

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('uploads', 'public');

            $data['file_path']     = $path;
            $data['file_type']     = $file->getClientOriginalExtension();
            $data['file_size']     = $file->getSize();
            $data['original_name'] = $file->getClientOriginalName();
        }

        $document->update($data);

        // update timestamp folder
        $document->folder?->touch();

        return redirect()->route('folders.show', $document->folder_id)
                         ->with('success', 'File berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $folder_id = $document->folder_id;

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        Folder::find($folder_id)?->touch();

        return redirect()->route('folders.show', $folder_id)
                         ->with('success', 'File berhasil dihapus.');
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);

        $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));

        if (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'csv', 'zip', 'rar'])) {
            $path = storage_path('app/public/' . $document->file_path);
            if (file_exists($path)) {
                return response()->download($path, $document->original_name);
            } else {
                abort(404, 'File tidak ditemukan.');
            }
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
}
