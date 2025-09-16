<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kesalahan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-slate-50 min-h-screen font-sans">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $message }}</h1>
            <p class="text-gray-600 mb-6">{{ $subtitle }}</p>
            
            <div class="space-y-3">
                <button onclick="history.back()" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Kembali
                </button>
                <button onclick="window.location.reload()" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Coba Lagi
                </button>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Jika masalah berlanjut, silakan hubungi administrator sistem.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
