<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Biofarma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .icon-eye {
            width: 22px;
            height: 22px;
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 67%;
            transform: translateY(-50%);
        }
        .relative-input {
            position: relative;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-cyan-200 to-white min-h-screen flex items-center justify-center px-4">

    <div class="flex flex-col md:flex-row w-full max-w-5xl bg-transparent relative">
        <div class="absolute top-0 left-0 w-[250px] h-[250px] rounded-full bg-cyan-300 opacity-20 animate-pulse blur-3xl"></div>

        <div class="flex-1 flex items-center justify-center mb-6 md:mb-0">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Biofarma" class="w-40 md:w-72 h-auto">
        </div>

        <div class="flex-1 flex items-center justify-center">
            <div class="bg-white shadow-lg rounded-xl p-8 md:p-10 w-full max-w-md">
                <h2 class="text-2xl font-bold text-[#029dbb] mb-6 text-center">Reset Password</h2>

                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">

                    <!-- PASSWORD BARU -->
                    <div class="mb-5 relative-input">
                        <label for="password" class="block text-sm font-semibold text-[#029dbb] mb-2">Password Baru</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg pr-12 focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
                        <img src="{{ asset('assets/icons/eye.png') }}" 
                             alt="Show" class="icon-eye" id="togglePassword">
                    </div>

                    <!-- KONFIRMASI PASSWORD -->
                    <div class="mb-6 relative-input">
                        <label for="password_confirmation" class="block text-sm font-semibold text-[#029dbb] mb-2">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg pr-12 focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
                        <img src="{{ asset('assets/icons/eye.png') }}" 
                             alt="Show" class="icon-eye" id="togglePasswordConfirm">
                    </div>

                    <button type="submit" 
                            class="w-full bg-[#029dbb] text-white py-3 rounded-lg hover:bg-[#027a96] transition duration-200 font-bold">
                        Reset Password
                    </button>
                </form>

                <div class="mt-5 text-center text-sm">
                    <a href="{{ route('login') }}" class="text-[#029dbb] hover:underline">Kembali ke login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        togglePassword.addEventListener('click', () => {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            togglePassword.src = isHidden
                ? "{{ asset('assets/icons/hidden.png') }}"
                : "{{ asset('assets/icons/eye.png') }}";
        });

        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirm = document.getElementById('password_confirmation');
        togglePasswordConfirm.addEventListener('click', () => {
            const isHidden = passwordConfirm.type === 'password';
            passwordConfirm.type = isHidden ? 'text' : 'password';
            togglePasswordConfirm.src = isHidden
                ? "{{ asset('assets/icons/hidden.png') }}"
                : "{{ asset('assets/icons/eye.png') }}";
        });
    </script>

</body>
</html>
