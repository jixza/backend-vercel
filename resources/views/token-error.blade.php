<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Farmakogenetik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-server text-3xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Terjadi Kesalahan Server</h1>
            
            <p class="text-gray-600 mb-6">
                {{ $message ?? 'Maaf, terjadi kesalahan saat memuat data pasien.' }}
            </p>
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Apa yang bisa dilakukan:</strong>
                        </p>
                        <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                            <li>Coba refresh halaman ini</li>
                            <li>Tunggu beberapa saat dan coba lagi</li>
                            <li>Hubungi petugas medis jika masalah berlanjut</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-redo mr-2"></i>Coba Lagi
            </button>
        </div>
    </div>
</body>
</html>