<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function show()
    {
        $hour = Carbon::now('Asia/Jakarta')->format('H');
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Selamat pagi!';
        } elseif ($hour >= 12 && $hour < 15) {
            $greeting = 'Selamat siang!';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat sore!';
        } else {
            $greeting = 'Selamat malam!';
        }

        return view('profile.show', [
            'user' => Auth::user(),
            'greeting' => $greeting
        ]);
    }

    public function editName()
    {
        return view('profile.edit-name', [
            'user' => Auth::user()
        ]);
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return redirect()->route('profile')->with('success', 'Nama berhasil diperbarui!');
    }

    public function editPassword()
    {
        return view('profile.edit-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ], [
            'password.regex' => 'Kata sandi baru harus mengandung minimal satu huruf kapital, satu angka, dan satu simbol.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui!');
    }

    public function editPhoto()
    {
        return view('profile.edit-photo', [
            'user' => Auth::user()
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        // Simpan foto baru di storage/app/public/profile-photos
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->photo = $path;
        $user->save();

        return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function photo($id)
    {
        $user = User::findOrFail($id);

        if (!$user->photo) {
            abort(404);
        }

        $path = storage_path('app/public/' . $user->photo);
        abort_unless(is_file($path), 404);

        $mime = mime_content_type($path) ?: 'image/jpeg';

        return response()->file($path, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'private, max-age=0, no-store',
        ]);
    }
}
