<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;

class FolderController extends Controller
{
    /**
     * Menampilkan dashboard folder.
     */
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

    /**
     * Menampilkan form create folder.
     */
    public function create()
    {
        return view('dashboard.create-folder');
    }

    /**
     * Simpan folder baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tna_code' => 'nullable|string|max:255|unique:folders,tna_code',
        ], [
            'tna_code.unique' => 'Kode TNA ini sudah digunakan oleh folder lain.'
        ]);

        Folder::create($request->only(['title', 'description', 'tna_code']));

        return redirect()->route('dashboard')->with('success', 'Folder berhasil dibuat.');
    }

    /**
     * Menampilkan folder tertentu.
     */
    public function show($id)
    {
        $folder = Folder::findOrFail($id);

        $documents = $folder->documents();

        if (request('jenis_file')) {
            $documents->where('jenis_file', request('jenis_file'));
        }

        if (request('search')) {
            $documents->where('title', 'like', '%' . request('search') . '%');
        }

        $folder->documents = $documents->get();

        $jenisFiles = $folder->documents()
            ->select('jenis_file')
            ->distinct()
            ->pluck('jenis_file');

        return view('dashboard.show-folder', compact('folder', 'jenisFiles'));
    }

    /**
     * Edit folder.
     */
    public function edit($id)
    {
        $folder = Folder::findOrFail($id);
        return view('dashboard.edit-folder', compact('folder'));
    }

    /**
     * Update folder.
     */
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

        return redirect()->route('dashboard')->with('success', 'Folder berhasil diperbarui.');
    }

    /**
     * Hapus folder.
     */
    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();

        return redirect()->route('dashboard')->with('success', 'Folder berhasil dihapus.');
    }
}
