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
        $query = Document::where('folder_id', $id);

        \Log::info('Filter parameters', [
            'search' => $request->get('search'),
            'jenis_file' => $request->get('jenis_file'),
            'dari_tanggal' => $request->get('dari_tanggal'),
            'sampai_tanggal' => $request->get('sampai_tanggal')
        ]);

        // Filter berdasarkan pencarian
        if ($request->filled('search') && !empty($request->get('search'))) {
            $searchTerm = $request->get('search');
            $query->where('title', 'like', '%' . $searchTerm . '%');
            \Log::info('Applied search filter: ' . $searchTerm);
        }

        // Filter berdasarkan jenis file
        if ($request->filled('jenis_file') && !empty($request->get('jenis_file'))) {
            $jenisFile = $request->get('jenis_file');
            $query->where('jenis_file', $jenisFile);
            \Log::info('Applied jenis_file filter: ' . $jenisFile);
        }

        //Filter berdasarkan rentang tanggal
        if ($request->filled('dari_tanggal') || $request->filled('sampai_tanggal')) {
            $dariTanggal = $request->get('dari_tanggal');
            $sampaiTanggal = $request->get('sampai_tanggal');
            
            \Log::info('Applying date range filter', [
                'dari_tanggal' => $dariTanggal,
                'sampai_tanggal' => $sampaiTanggal
            ]);

            $query->where(function($q) use ($dariTanggal, $sampaiTanggal) {
                // Ambil semua dokumen yang memiliki waktu_pelaksanaan
                $q->whereNotNull('waktu_pelaksanaan')
                  ->where('waktu_pelaksanaan', '!=', '');
                
                // Filter berdasarkan tanggal menggunakan callback
                $q->where(function($subQuery) use ($dariTanggal, $sampaiTanggal) {
                    $subQuery->whereRaw('1=1');
                });
            });
        }

        // Ambil semua dokumen sesuai filter dasar
        $allDocuments = $query->orderBy('created_at', 'desc')->get();

        if ($request->filled('dari_tanggal') || $request->filled('sampai_tanggal')) {
            $dariTanggal = $request->get('dari_tanggal');
            $sampaiTanggal = $request->get('sampai_tanggal');
            
            $filteredDocuments = $allDocuments->filter(function($doc) use ($dariTanggal, $sampaiTanggal) {
                if (!$doc->waktu_pelaksanaan) {
                    return false;
                }
                
                try {
                    $waktuPelaksanaan = $this->parseIndonesianDate($doc->waktu_pelaksanaan);
                    
                    if (!$waktuPelaksanaan) {
                        \Log::warning('Cannot parse date for document ' . $doc->title . ': ' . $doc->waktu_pelaksanaan);
                        return false;
                    }
                    
                    $shouldInclude = true;
                    
                    if ($dariTanggal) {
                        $startDate = Carbon::parse($dariTanggal)->startOfDay();
                        if ($waktuPelaksanaan->lt($startDate)) {
                            $shouldInclude = false;
                        }
                    }
                    
                    if ($sampaiTanggal && $shouldInclude) {
                        $endDate = Carbon::parse($sampaiTanggal)->endOfDay();
                        if ($waktuPelaksanaan->gt($endDate)) {
                            $shouldInclude = false;
                        }
                    }
                    
                    \Log::info('Date filter check', [
                        'document' => $doc->title,
                        'waktu_pelaksanaan' => $doc->waktu_pelaksanaan,
                        'parsed_date' => $waktuPelaksanaan->format('Y-m-d'),
                        'dari_tanggal' => $dariTanggal,
                        'sampai_tanggal' => $sampaiTanggal,
                        'included' => $shouldInclude ? 'Yes' : 'No'
                    ]);
                    
                    return $shouldInclude;
                    
                } catch (\Exception $e) {
                    \Log::warning('Failed to parse date for document ' . $doc->title . ': ' . $doc->waktu_pelaksanaan . ' - Error: ' . $e->getMessage());
                    return false;
                }
            });
            
            $folder->documents = $filteredDocuments;
        } else {
            $folder->documents = $allDocuments;
        }

        // Ambil semua jenis file untuk dropdown filter
        $jenisFiles = Document::where('folder_id', $id)
                            ->select('jenis_file')
                            ->distinct()
                            ->whereNotNull('jenis_file')
                            ->where('jenis_file', '!=', '')
                            ->pluck('jenis_file');

        return view('dashboard.show-folder', compact('folder', 'jenisFiles'));
    }

    private function parseIndonesianDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        $dateString = trim($dateString);
        
        // Mapping bulan Indonesia ke angka
        $months = [
            'januari' => '01', 'jan' => '01',
            'februari' => '02', 'feb' => '02',
            'maret' => '03', 'mar' => '03',
            'april' => '04', 'apr' => '04',
            'mei' => '05',
            'juni' => '06', 'jun' => '06',
            'juli' => '07', 'jul' => '07',
            'agustus' => '08', 'agt' => '08', 'aug' => '08',
            'september' => '09', 'sep' => '09',
            'oktober' => '10', 'okt' => '10', 'oct' => '10',
            'november' => '11', 'nov' => '11',
            'desember' => '12', 'des' => '12', 'dec' => '12'
        ];

        try {
            // Pattern untuk format: "5 juni 2025", "12 Agustus 2025", "23 Juni 2025"
            if (preg_match('/(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})/i', $dateString, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $monthName = strtolower($matches[2]);
                $year = $matches[3];
                
                if (isset($months[$monthName])) {
                    $month = $months[$monthName];
                    $formattedDate = $year . '-' . $month . '-' . $day;
                    
                    \Log::info('Parsing date (text format): ' . $dateString . ' -> ' . $formattedDate);
                    return Carbon::createFromFormat('Y-m-d', $formattedDate);
                }
            }
            
            // Pattern untuk format dengan tanda hubung: "5-6-2025", "12-08-2025"
            if (preg_match('/(\d{1,2})-(\d{1,2})-(\d{4})/i', $dateString, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                
                $formattedDate = $year . '-' . $month . '-' . $day;
                \Log::info('Parsing date (dash format): ' . $dateString . ' -> ' . $formattedDate);
                return Carbon::createFromFormat('Y-m-d', $formattedDate);
            }
            
            // Pattern untuk format dengan slash: "5/6/2025", "12/08/2025"
            if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/i', $dateString, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                
                $formattedDate = $year . '-' . $month . '-' . $day;
                \Log::info('Parsing date (slash format): ' . $dateString . ' -> ' . $formattedDate);
                return Carbon::createFromFormat('Y-m-d', $formattedDate);
            }
            
            // Pattern untuk format ISO: "2025-01-20", "2025-12-31"
            if (preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})/i', $dateString, $matches)) {
                $year = $matches[1];
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
                
                $formattedDate = $year . '-' . $month . '-' . $day;
                \Log::info('Parsing date (ISO format): ' . $dateString . ' -> ' . $formattedDate);
                return Carbon::createFromFormat('Y-m-d', $formattedDate);
            }
            
            return Carbon::parse($dateString);
            
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Indonesian date: ' . $dateString . ' - Error: ' . $e->getMessage());
            return null;
        }
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
        
        foreach ($folder->documents as $document) {
            if ($document->file_path) {
                \Storage::disk('public')->delete($document->file_path);
            }
        }
        
        $folder->delete();
        return redirect()->route('dashboard')->with('success', 'Folder berhasil dihapus.');
    }
}