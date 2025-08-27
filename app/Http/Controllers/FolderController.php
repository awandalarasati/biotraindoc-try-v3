<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Document;
use Carbon\Carbon;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $query = Folder::with('documents');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('tna_code', 'like', '%' . $request->search . '%');
            });
        }

        $folders = $query->get();

        foreach ($folders as $folder) {
            $totalSizeBytes = $folder->documents->sum('file_size');
            $folder->folder_size = $totalSizeBytes
                ? round($totalSizeBytes / (1024 * 1024), 2) . ' MB'
                : '0 MB';
        }

        return view('dashboard.index', compact('folders'));
    }

    public function create()
    {
        return view('dashboard.create-folder');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tna_code' => 'nullable|string|max:255|unique:folders,tna_code',
        ], [
            'tna_code.unique' => 'Kode TNA ini sudah digunakan oleh folder lain.'
        ]);

        $folder = Folder::create($request->only(['title', 'description', 'tna_code']));
        $folder->touch();

        return redirect()->route('dashboard')->with('success', 'Folder berhasil dibuat.');
    }

    public function show($id, Request $request)
    {
        $folder = Folder::findOrFail($id);

        // Query dasar untuk dokumen dalam folder
        $query = Document::where('folder_id', $id);

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan jenis file
        if ($request->filled('jenis_file')) {
            $query->where('jenis_file', $request->jenis_file);
        }

        // Filter berdasarkan waktu pelaksanaan
        if ($request->filled('waktu_pelaksanaan')) {
            $filterWaktu = $request->waktu_pelaksanaan;
            $now = Carbon::now();

            switch ($filterWaktu) {
                case '1_bulan':
                    $startDate = $now->copy()->subMonth();
                    $query->where('created_at', '>=', $startDate);
                    break;
                case '3_bulan':
                    $startDate = $now->copy()->subMonths(3);
                    $query->where('created_at', '>=', $startDate);
                    break;
                case '6_bulan':
                    $startDate = $now->copy()->subMonths(6);
                    $query->where('created_at', '>=', $startDate);
                    break;
            }
        }

        // Ambil hasil query dan assign ke folder
        $documents = $query->orderBy('created_at', 'desc')->get();
        $folder->documents = $documents;

        // Ambil semua jenis file untuk dropdown filter (dari semua dokumen di folder, bukan yang sudah difilter)
        $jenisFiles = Document::where('folder_id', $id)
                            ->select('jenis_file')
                            ->distinct()
                            ->whereNotNull('jenis_file')
                            ->pluck('jenis_file');

        return view('dashboard.show-folder', compact('folder', 'jenisFiles'));
    }

    public function edit($id)
    {
        $folder = Folder::findOrFail($id);
        return view('dashboard.edit-folder', compact('folder'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tna_code' => 'nullable|string|max:255|unique:folders,tna_code,' . $id,
        ], [
            'tna_code.unique' => 'Kode TNA ini sudah digunakan oleh folder lain.'
        ]);

        $folder = Folder::findOrFail($id);
        $folder->update($request->only(['title', 'description', 'tna_code']));
        $folder->touch();

        return redirect()->route('dashboard')->with('success', 'Folder berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        
        // Hapus semua file yang terkait
        foreach ($folder->documents as $document) {
            if ($document->file_path) {
                \Storage::disk('public')->delete($document->file_path);
            }
        }
        
        $folder->delete();
        return redirect()->route('dashboard')->with('success', 'Folder berhasil dihapus.');
    }
}