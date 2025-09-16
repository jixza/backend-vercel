<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Medis Pasien - PG Card</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .medical-badge {
            background: linear-gradient(45deg, #4ade80, #22c55e);
        }
    </style>
</head>
<body class="bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="medical-badge w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-user-md text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Data Medis Pasien</h1>
            <p class="text-white/80">Informasi medis dan hasil tes farmakogenetik</p>
        </div>

        <!-- Patient Info Card -->
        <div class="card rounded-2xl shadow-2xl p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $patientData['patient_name'] ?? 'Nama Pasien' }}</h2>
                    <p class="text-gray-600">{{ $patientData['patient_data'] ?? 'Data pasien tidak tersedia' }}</p>
                </div>
            </div>

            <!-- Medical Information Grid -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Physical Data -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-weight text-indigo-600 mr-2"></i>
                        Data Fisik
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tinggi Badan:</span>
                            <span class="font-medium">{{ $patientData['height'] ?? '-' }} cm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Berat Badan:</span>
                            <span class="font-medium">{{ $patientData['weight'] ?? '-' }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">BMI:</span>
                            <span class="font-medium">{{ $patientData['bmi'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Genetic Data -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-dna text-green-600 mr-2"></i>
                        Data Genetik
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">IRS1 rs1801278:</span>
                            <span class="font-medium bg-green-100 text-green-800 px-2 py-1 rounded">
                                {{ $patientData['irs1_rs1801278'] ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Drug Allergies -->
            @if(!empty($patientData['drug_allergies']))
            <div class="bg-red-50 border-l-4 border-red-400 rounded-xl p-6 mb-6">
                <h3 class="font-semibold text-red-800 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    Alergi Obat
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($patientData['drug_allergies'] as $allergy)
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">{{ $allergy }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Current Medications -->
            @if(!empty($patientData['drugs_consumed']))
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <h3 class="font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-pills text-blue-600 mr-2"></i>
                    Obat yang Dikonsumsi
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($patientData['drugs_consumed'] as $drug)
                    <div class="bg-white border border-blue-200 rounded-lg p-3 text-center">
                        <span class="text-blue-800 font-medium">{{ $drug }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Prescription -->
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-prescription text-purple-600 mr-2"></i>
                    Resep
                </h3>
                <p class="text-gray-700">{{ $patientData['prescription'] ?? 'Tidak ada resep' }}</p>
            </div>

            <!-- Diabetes Info -->
            @if($patientData['diabetes_diagnosed_since'] !== '-')
            <div class="bg-orange-50 rounded-xl p-6">
                <h3 class="font-semibold text-orange-800 mb-3 flex items-center">
                    <i class="fas fa-heartbeat text-orange-600 mr-2"></i>
                    Informasi Diabetes
                </h3>
                <p class="text-orange-700">
                    <span class="font-medium">Didiagnosis sejak:</span> 
                    {{ $patientData['diabetes_diagnosed_since'] }}
                </p>
            </div>
            @endif
        </div>

        <!-- Security Notice -->
        <div class="card rounded-xl p-6 text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-yellow-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">Keamanan Data</h3>
            <p class="text-gray-600 text-sm">
                Link ini hanya dapat diakses sekali untuk melindungi privasi data medis Anda. 
                Setelah halaman ini ditutup, link akan otomatis kedaluwarsa.
            </p>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white/80">
            <p class="text-sm">
                <i class="fas fa-lock mr-1"></i>
                Data dilindungi dengan enkripsi end-to-end
            </p>
            <p class="text-xs mt-2">PG Card - Sistem Informasi Farmakogenetik © {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        // Auto-refresh warning after 30 minutes
        setTimeout(() => {
            if (confirm('Sesi akan berakhir dalam 5 menit. Apakah Anda ingin memperpanjang?')) {
                location.reload();
            }
        }, 25 * 60 * 1000); // 25 minutes

        // Prevent context menu and selection for security
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
    </script>
</body>
</html>