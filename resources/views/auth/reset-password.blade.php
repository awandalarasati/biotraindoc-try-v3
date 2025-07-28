<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Biofarma</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-cyan-200 to-white min-h-screen flex items-center justify-center">

    <div class="flex w-full max-w-5xl bg-transparent relative">
        <!-- Efek animasi lingkaran -->
        <div class="absolute top-0 left-0 w-[300px] h-[300px] rounded-full bg-cyan-300 opacity-20 animate-pulse blur-3xl"></div>

        <!-- Bagian kiri (logo Biofarma) -->
        <div class="flex-1 flex items-center justify-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Biofarma" class="w-[280px] h-auto">
        </div>

        <!-- Bagian kanan (form reset password) -->
        <div class="flex-1 flex items-center justify-center">
            <div class="bg-white shadow-lg rounded-xl p-10 w-full max-w-md">
                <h2 class="text-2xl font-bold text-[#029dbb] mb-6 text-center">Reset Password</h2>

                <!-- ✅ Pesan error kalau ada -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- ✅ FORM RESET PASSWORD -->
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">

                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-[#029dbb] mb-2">Password Baru</label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-3 border rounded-lg">
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-semibold text-[#029dbb] mb-2">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-3 border rounded-lg">
                    </div>

                    <button type="submit" class="w-full bg-[#029dbb] text-white py-3 rounded-lg">
                        Reset Password
                    </button>
                </form>


                <!-- Tombol kembali ke login -->
                <div class="mt-5 text-center text-sm">
                    <a href="{{ route('login') }}" class="text-[#029dbb] hover:underline">Kembali ke login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
