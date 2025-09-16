<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Tidak Valid</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50 min-h-screen font-sans">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $message }}</h1>
            <p class="text-gray-600 mb-6">{{ $subtitle }}</p>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-red-700">
                    <strong>Kemungkinan penyebab:</strong>
                </p>
                <ul class="text-sm text-red-600 mt-2 space-y-1">
                    <li>• QR Code sudah kedaluwarsa</li>
                    <li>• QR Code sudah pernah digunakan</li>
                    <li>• Link QR Code tidak valid</li>
                </ul>
            </div>
            
            <div class="space-y-3">
                <button onclick="history.back()" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Kembali
                </button>
                <button onclick="window.location.reload()" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Coba Lagi
                </button>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Jika masalah berlanjut, hubungi pemilik QR Code untuk mendapatkan QR Code yang baru.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
