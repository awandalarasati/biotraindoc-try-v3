<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Biofarma</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-cyan-200 to-white min-h-screen flex items-center justify-center px-4">

    <div class="flex flex-col md:flex-row w-full max-w-5xl bg-transparent relative">
        <div class="absolute top-0 left-0 w-[250px] h-[250px] rounded-full bg-cyan-300 opacity-20 animate-pulse blur-3xl"></div>

        <!-- Logo -->
        <div class="flex-1 flex items-center justify-center mb-6 md:mb-0">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Biofarma" class="w-40 md:w-72 h-auto">
        </div>

        <!-- Form Forgot Password -->
        <div class="flex-1 flex items-center justify-center">
            <div class="bg-white shadow-lg rounded-xl p-8 md:p-10 w-full max-w-md">
                <h2 class="text-2xl font-bold text-[#029dbb] mb-6 text-center">Lupa Kata Sandi?</h2>
                
                @if (session('status'))
                    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-[#029dbb] mb-2">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan email Anda"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
                    </div>

                    <button type="submit" class="w-full bg-[#029dbb] text-white py-3 rounded-lg hover:bg-[#027a96] transition font-bold">
                        Kirim Link Reset
                    </button>
                </form>

                <div class="mt-5 text-center text-sm">
                    <a href="{{ route('login') }}" class="text-[#029dbb] hover:underline">Kembali ke login</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
