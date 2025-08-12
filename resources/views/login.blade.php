<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/login.js')
    <title>Login</title>
</head>

<body class="bg-blue-50 font-[Poppins]">
    <div class="flex min-h-screen">
        <!-- Kiri (Form Login) - 60% -->
        <div class="w-full md:w-[60%] flex flex-col justify-center login-container">
            <h1 class="text-5xl font-bold text-black mb-2">Selamat datang!</h1>
            <p class="text-gray-600 mb-6 text-left">Silakan masuk untuk manajemen absensi</p>

            <div id="alert-box" class="hidden text-red-500"></div>
            <form id="login-form" class="w-full max-w-lg space-y-4 mt-5">
                @csrf
                <input type="text" name="username" placeholder="Username"
                    class="w-full p-4 bg-white rounded-lg shadow-input border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required>

                <input type="password" name="password" placeholder="Kata Sandi"
                    class="w-full p-4 bg-white rounded-lg shadow-input border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required>

                <button type="submit"
                    class="w-full p-4 mt-4 bg-[#60B5FF] hover:bg-blue-400 text-white font-semibold rounded-lg transition-all duration-200">
                    Masuk
                </button>
            </form>
        </div>

        <!-- Kanan (Gambar) - 40% -->
       <div class="hidden md:block w-[40%] login-image rounded-l-extra"></div>

    </div>
</body>

</html>
