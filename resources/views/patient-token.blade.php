<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $patientData['patient_name'] ?? 'Patient Data' }} - Farmakogenetik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-green { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .card-shadow { box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-blue text-white">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-dna text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Farmakogenetik</h1>
                    <p class="text-blue-100">Rekam Medis Pasien</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Header -->
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-gradient-blue text-white rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ substr($patientData['patient_name'] ?? 'P', 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $patientData['patient_name'] ?? 'Unknown Patient' }}</h2>
                        <p class="text-gray-600">{{ $patientData['patient_data'] ?? 'N/A' }}</p>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $patientData['patient_data'] ?? 'N/A' }}</span>
                            <span><i class="fas fa-user mr-1"></i>Data Pasien</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Pasien Aktif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Akses Aman</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Height -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $patientData['height'] ?? 'N/A' }}</div>
                    <div class="text-gray-500 text-sm">Tinggi (cm)</div>
                    <div class="text-xs text-gray-400 mt-1">Normal</div>
                </div>
            </div>
            
            <!-- Weight -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $patientData['weight'] ?? 'N/A' }}</div>
                    <div class="text-gray-500 text-sm">Berat (kg)</div>
                    <div class="text-xs text-gray-400 mt-1">Normal</div>
                </div>
            </div>
            
            <!-- BMI -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $patientData['bmi'] ?? 'N/A' }}</div>
                    <div class="text-gray-500 text-sm">IMT</div>
                    <div class="text-xs text-gray-400 mt-1">Normal</div>
                </div>
            </div>
            
            <!-- Diabetes Status -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-xl font-bold text-red-600">Tipe 2</div>
                    <div class="text-gray-500 text-sm">Status Diabetes</div>
                    <div class="text-xs text-gray-400 mt-1">{{ isset($patientData['diabetes_diagnosis_date']) && $patientData['diabetes_diagnosis_date'] !== '-' ? 'Confirmed' : 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Lab Results -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-vial text-blue-500 mr-2"></i>Hasil Genetik & Lab
                </h3>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-xl font-bold text-purple-600">{{ $patientData['irs1_rs1801278'] ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Varian Genetik</div>
                        <div class="text-xs text-gray-500">IRS1 rs1801278</div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-stethoscope text-red-500 mr-2"></i>Diagnosis Utama
                </h3>
                
                <div class="bg-red-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <div>
                            <h4 class="font-semibold text-red-800">Diabetes Mellitus Type 2</h4>
                            <p class="text-red-600 text-sm">Confirmed diagnosis</p>
                            <p class="text-red-500 text-xs">ICD-10: E11</p>
                        </div>
                    </div>
                </div>

                @if(isset($patientData['diabetes_diagnosis_date']) && $patientData['diabetes_diagnosis_date'] !== '-')
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Diabetes didiagnosis sejak: <strong>{{ $patientData['diabetes_diagnosis_date'] }}</strong>
                    </p>
                </div>
                @endif
            </div>

            <!-- Current Medications -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-pills text-green-500 mr-2"></i>Obat yang Dikonsumsi
                </h3>
                
                @if(!empty($patientData['drugs_consumed']))
                <div class="grid grid-cols-1 gap-3">
                    @foreach($patientData['drugs_consumed'] as $drug)
                    <div class="bg-green-50 rounded-lg p-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <div>
                                <h4 class="font-semibold text-green-800">{{ $drug }}</h4>
                                <p class="text-green-600 text-sm">Obat aktif</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-info-circle text-gray-400 text-3xl mb-2"></i>
                    <p class="text-gray-500">Tidak ada obat yang sedang dikonsumsi</p>
                </div>
                @endif
            </div>

            <!-- Allergies -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>Alergi Obat
                </h3>
                
                @if(!empty($patientData['drug_allergies']))
                <div class="space-y-3">
                    @foreach($patientData['drug_allergies'] as $allergy)
                    <div class="bg-orange-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                            <div>
                                <h4 class="font-semibold text-orange-800">{{ $allergy }}</h4>
                                <p class="text-orange-600 text-sm">Alergi diketahui</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                    <p class="text-green-600 font-medium">Tidak ada alergi obat yang diketahui.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Prescription -->
        <div class="bg-white rounded-lg card-shadow p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-prescription text-purple-500 mr-2"></i>Resep Terkini
            </h3>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-purple-700">{{ $patientData['prescription'] ?? 'Tidak ada resep tersedia' }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-white rounded-lg card-shadow p-6 mt-6">
            <div class="text-center text-gray-500 text-sm">
                <p class="mb-2">
                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                    Data ini diakses melalui token aman yang dibuat oleh <strong>{{ $tokenInfo['created_by'] ?? 'System' }}</strong>
                </p>
                <p class="text-xs">
                    Dibuat: {{ isset($tokenInfo['created_at']) ? $tokenInfo['created_at']->format('d M Y H:i') : 'N/A' }} | 
                    Kedaluwarsa: {{ isset($tokenInfo['expires_at']) ? $tokenInfo['expires_at']->format('d M Y H:i') : 'N/A' }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Terakhir diperbarui: {{ now()->format('d M Y H:i') }} | 
                    Dokumen ini bersifat rahasia dan hanya untuk keluarga pasien
                </p>
                <p class="text-xs text-red-500 mt-2">
                    <i class="fas fa-lock mr-1"></i>
                    {{ $tokenInfo['message'] ?? 'Token telah digunakan dan tidak bisa diakses lagi' }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // Prevent context menu and selection for security
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        
        // Auto-refresh warning after 30 minutes
        setTimeout(() => {
            if (confirm('Sesi akan berakhir dalam 5 menit. Apakah Anda ingin memperpanjang?')) {
                location.reload();
            }
        }, 25 * 60 * 1000); // 25 minutes
    </script>
</body>
</html>
<body class="bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-user-md text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Data Medis Pasien</h1>
            <p class="text-gray-600">Informasi medis dan hasil tes farmakogenetik</p>
        </div>

        <!-- Patient Info Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
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
            @if(isset($patientData['diabetes_diagnosis_date']) && $patientData['diabetes_diagnosis_date'] !== '-')
            <div class="bg-orange-50 rounded-xl p-6">
                <h3 class="font-semibold text-orange-800 mb-3 flex items-center">
                    <i class="fas fa-heartbeat text-orange-600 mr-2"></i>
                    Informasi Diabetes
                </h3>
                <p class="text-orange-700">
                    <span class="font-medium">Didiagnosis sejak:</span> 
                    {{ $patientData['diabetes_diagnosis_date'] }}
                </p>
            </div>
            @endif
        </div>

        <!-- Security Notice -->
        <div class="bg-white rounded-xl p-6 text-center">
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
        <div class="text-center mt-8 text-gray-500">
            <p class="text-sm">
                <i class="fas fa-lock mr-1"></i>
                Data dilindungi dengan enkripsi end-to-end
            </p>
            <p class="text-xs mt-2">Farmakogenetik © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>