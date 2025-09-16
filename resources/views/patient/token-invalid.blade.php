<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Tidak Valid - Farmakogenetik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Token Tidak Valid</h1>
            
            <p class="text-gray-600 mb-6">
                {{ $message ?? 'Token yang Anda gunakan tidak valid atau sudah kedaluwarsa.' }}
            </p>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Kemungkinan penyebab:</strong>
                        </p>
                        <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                            <li>Token sudah kedaluwarsa</li>
                            <li>Token sudah digunakan (jika one-time use)</li>
                            <li>Token tidak valid atau rusak</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-500">
                Silakan hubungi petugas medis untuk mendapatkan token baru.
            </p>
        </div>
    </div>
</body>
</html>