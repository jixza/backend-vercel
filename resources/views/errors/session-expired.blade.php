<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-orange-50 to-red-50 min-h-screen font-sans">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 15v2m-6 0h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $message }}</h1>
            <p class="text-gray-600 mb-4">{{ $subtitle }}</p>
            
            @if(isset($creator_name))
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-orange-700">
                    <strong>Pemilik akun:</strong> {{ $creator_name }}
                </p>
                <p class="text-xs text-orange-600 mt-1">
                    Pemilik akun telah logout dari sistem
                </p>
            </div>
            @endif
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-700">
                    <strong>Untuk mengakses data pasien:</strong>
                </p>
                <ul class="text-sm text-blue-600 mt-2 space-y-1">
                    <li>• Minta pemilik akun untuk login kembali</li>
                    <li>• Minta QR Code yang baru</li>
                    <li>• Pastikan pemilik akun masih aktif</li>
                </ul>
            </div>
            
            <div class="space-y-3">
                <button onclick="history.back()" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Kembali
                </button>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Kebijakan keamanan: QR Code otomatis tidak valid saat pemilik akun logout
                </p>
            </div>
        </div>
    </div>
</body>
</html>
