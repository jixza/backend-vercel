<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $patient['info']['name'] ?? 'Patient Data' }} - Farmakogenetik</title>
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
                        {{ $patient['info']['initials'] ?? 'P' }}
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $patient['info']['name'] ?? 'Unknown Patient' }}</h2>
                        <p class="text-gray-600">{{ $patient['info']['medical_record_number'] ?? 'N/A' }}</p>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $patient['info']['dob'] ?? 'N/A' }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $patient['info']['gender'] ?? 'N/A' }}</span>
                            <span><i class="fas fa-phone mr-1"></i>{{ $patient['info']['phone'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Pasien Aktif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">ID Pasien #{{ $patient['info']['id'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        @if(isset($patient['latest_record']) && $patient['latest_record'])
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Height -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $patient['latest_record']['height'] ?? 'N/A' }}</div>
                    <div class="text-gray-500 text-sm">Tinggi (cm)</div>
                    <div class="text-xs text-gray-400 mt-1">Normal</div>
                </div>
            </div>
            
            <!-- Weight -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $patient['latest_record']['weight'] ?? 'N/A' }}</div>
                    <div class="text-gray-500 text-sm">Berat (kg)</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $patient['latest_record']['bmi_status'] ?? 'N/A' }}</div>
                </div>
            </div>
            
            <!-- BMI -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $patient['latest_record']['bmi'] ?? 'N/A' }}</div>
                    <div class="text-gray-500 text-sm">IMT</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $patient['latest_record']['bmi_status'] ?? 'Normal' }}</div>
                </div>
            </div>
            
            <!-- Diabetes Status -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <div class="text-center">
                    <div class="text-xl font-bold text-red-600">Tipe 2</div>
                    <div class="text-gray-500 text-sm">Status Diabetes</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $patient['latest_record']['diabetes_status'] ?? 'Confirmed' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Lab Results -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-vial text-blue-500 mr-2"></i>Gula Darah & Hasil Lab
                </h3>
                
                @if(isset($patient['latest_record']) && $patient['latest_record'])
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-800">{{ $patient['latest_record']['fasting_blood_sugar'] ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Gula Darah Puasa</div>
                        <div class="text-xs text-gray-500">mg/dL</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-800">{{ $patient['latest_record']['standard_blood_sugar'] ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Gula Darah Sewaktu</div>
                        <div class="text-xs text-gray-500">mg/dL</div>
                    </div>
                    
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-xl font-bold text-yellow-600">{{ $patient['latest_record']['hba1c'] ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Hasil HbA1c</div>
                        <div class="text-xs text-gray-500">%</div>
                    </div>
                    
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-lg font-bold text-blue-600">{{ $patient['latest_record']['irs1_variant'] ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Varian Genetik</div>
                        <div class="text-xs text-gray-500">IRS1 rs1801278</div>
                    </div>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">Belum ada data lab tersedia</p>
                @endif
            </div>

            <!-- Diagnosis -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-stethoscope text-red-500 mr-2"></i>Diagnosis Utama
                </h3>
                
                @if(isset($patient['diagnoses']['primary']) && $patient['diagnoses']['primary'])
                <div class="bg-red-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <div>
                            <h4 class="font-semibold text-red-800">{{ $patient['diagnoses']['primary']['name'] ?? 'Diabetes Mellitus Type 2' }}</h4>
                            <p class="text-red-600 text-sm">{{ $patient['diagnoses']['primary']['description'] ?? 'Confirmed diagnosis' }}</p>
                            <p class="text-red-500 text-xs">{{ $patient['diagnoses']['primary']['code'] ?? 'ICD-10: E11' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($patient['diabetes_diagnosis_date']) && $patient['diabetes_diagnosis_date'])
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Diabetes didiagnosis sejak: <strong>{{ \Carbon\Carbon::parse($patient['diabetes_diagnosis_date'])->format('d M Y') }}</strong>
                    </p>
                </div>
                @endif
            </div>

            <!-- Genetic Results -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-dna text-purple-500 mr-2"></i>Hasil Tes Genetik
                </h3>
                
                @if(isset($patient['genetic_results']) && count($patient['genetic_results']) > 0)
                    @foreach($patient['genetic_results'] as $result)
                    <div class="bg-purple-50 rounded-lg p-4 mb-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-purple-800">{{ $result['gene_name'] ?? 'BG12' }}</h4>
                                <p class="text-purple-600 text-sm">{{ $result['status'] ?? 'paru diminati lanjut' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">
                                    {{ $result['variant'] ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        @if(isset($result['description']))
                        <p class="text-purple-500 text-xs mt-2">{{ $result['description'] }}</p>
                        @endif
                    </div>
                    @endforeach
                @else
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-center">
                        <h4 class="font-semibold text-purple-800">BG12</h4>
                        <p class="text-purple-600 text-sm">paru diminati lanjut</p>
                        <p class="text-purple-500 text-xs mt-1">Genetic variant detected</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Allergies -->
            <div class="bg-white rounded-lg card-shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>Alergi Obat
                </h3>
                
                @if(isset($patient['allergies']) && count($patient['allergies']) > 0)
                    @foreach($patient['allergies'] as $allergy)
                    <div class="bg-orange-50 rounded-lg p-4 mb-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                            <div>
                                <h4 class="font-semibold text-orange-800">{{ $allergy['name'] }}</h4>
                                <p class="text-orange-600 text-sm">{{ $allergy['reaction'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                    <p class="text-green-600 font-medium">Tidak ada alergi obat yang diketahui.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Medical History -->
        @if(isset($patient['medical_history']) && count($patient['medical_history']) > 0)
        <div class="bg-white rounded-lg card-shadow p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-history text-gray-500 mr-2"></i>Riwayat Medis
            </h3>
            
            <div class="space-y-4">
                @foreach($patient['medical_history'] as $record)
                <div class="border-l-4 border-blue-200 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $record['title'] ?? 'Medical Visit' }}</h4>
                            <p class="text-gray-600 text-sm">Dr. {{ $record['doctor_name'] ?? 'Unknown' }} - {{ $record['doctor_specialization'] ?? 'General' }}</p>
                            @if(isset($record['prescription']) && $record['prescription'])
                            <p class="text-gray-500 text-sm mt-1">Resep: {{ $record['prescription'] }}</p>
                            @endif
                        </div>
                        <span class="text-gray-400 text-xs">{{ $record['date'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="bg-white rounded-lg card-shadow p-6 mt-6">
            <div class="text-center text-gray-500 text-sm">
                <p class="mb-2">
                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                    Data ini diakses melalui token aman yang dibuat oleh <strong>{{ $token_info['created_by'] ?? 'System' }}</strong>
                </p>
                <p class="text-xs">
                    Dibuat: {{ $token_info['created_at']->format('d M Y H:i') ?? 'N/A' }} | 
                    Kedaluwarsa: {{ $token_info['expires_at']->format('d M Y H:i') ?? 'N/A' }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Terakhir diperbarui: {{ now()->format('d M Y H:i') }} | 
                    Dokumen ini bersifat rahasia dan hanya untuk keluarga pasien
                </p>
            </div>
        </div>
    </div>
</body>
</html>