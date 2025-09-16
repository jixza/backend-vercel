<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Tidak Valid - PG Card</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            min-height: 100vh;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-md">
        <div class="card rounded-2xl shadow-2xl p-8 text-center">
            <!-- Error Icon -->
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>

            <!-- Error Title -->
            <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $error ?? 'Terjadi Kesalahan' }}</h1>
            
            <!-- Error Message -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                {{ $message ?? 'Link tidak valid atau sudah kedaluwarsa.' }}
            </p>

            <!-- Error Code -->
            @if(isset($code))
            <div class="bg-gray-100 rounded-lg p-3 mb-6">
                <span class="text-xs text-gray-500 uppercase tracking-wide">Kode Error</span>
                <div class="font-mono text-sm text-gray-700 mt-1">{{ $code }}</div>
            </div>
            @endif

            <!-- Action Instructions -->
            <div class="bg-blue-50 border-l-4 border-blue-400 rounded-xl p-4 mb-6 text-left">
                <h3 class="font-semibold text-blue-800 mb-2 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Apa yang bisa dilakukan:
                </h3>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Coba refresh halaman ini</li>
                    <li>• Tunggu beberapa saat dan coba lagi</li>
                    <li>• Hubungi petugas medis jika masalah berlanjut</li>
                </ul>
            </div>

            <!-- Retry Button -->
            <button onclick="location.reload()" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-200 mb-4">
                <i class="fas fa-redo-alt mr-2"></i>
                Coba Lagi
            </button>

            <!-- Security Notice -->
            <div class="text-xs text-gray-500 border-t pt-4">
                <i class="fas fa-shield-alt mr-1"></i>
                Untuk keamanan, link medis hanya berlaku untuk akses sekali
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white/80">
            <p class="text-xs">PG Card - Sistem Informasi Farmakogenetik © {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        // Auto-retry after 10 seconds (optional)
        let countdown = 10;
        function updateCountdown() {
            const retryBtn = document.querySelector('button');
            if (countdown > 0) {
                retryBtn.innerHTML = `<i class="fas fa-redo-alt mr-2"></i>Coba Lagi (${countdown}s)`;
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                retryBtn.innerHTML = '<i class="fas fa-redo-alt mr-2"></i>Coba Lagi';
            }
        }
        
        // Uncomment to enable auto-retry countdown
        // updateCountdown();
    </script>
</body>
</html>