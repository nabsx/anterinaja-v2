@extends('layouts.app')

@section('title', 'Kelola Dokumen')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Dokumen</h1>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Status Verifikasi -->
            <div class="mb-6">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium">Status Verifikasi:</span>
                    @if($driver->is_verified)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            Terverifikasi
                        </span>
                    @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                            Belum Terverifikasi
                        </span>
                    @endif
                </div>
            </div>

            <!-- Upload Documents -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- KTP -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">KTP (Kartu Tanda Penduduk)</h3>
                    @php
                        $ktpDoc = $documents->where('document_type', 'ktp')->first();
                    @endphp
                    
                    @if($ktpDoc)
                        <div class="mb-2">
                            <span class="text-sm text-green-600">✓ Sudah diunggah</span>
                            <div class="mt-2">
                                <img src="{{ Storage::url($ktpDoc->document_path) }}" alt="KTP" class="max-w-full h-32 object-cover rounded">
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('driver.documents.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="document_type" value="ktp">
                        <input type="file" name="document" accept="image/*,application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            {{ $ktpDoc ? 'Ganti KTP' : 'Upload KTP' }}
                        </button>
                    </form>
                </div>

                <!-- SIM -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">SIM (Surat Izin Mengemudi)</h3>
                    @php
                        $simDoc = $documents->where('document_type', 'sim')->first();
                    @endphp
                    
                    @if($simDoc)
                        <div class="mb-2">
                            <span class="text-sm text-green-600">✓ Sudah diunggah</span>
                            <div class="mt-2">
                                <img src="{{ Storage::url($simDoc->document_path) }}" alt="SIM" class="max-w-full h-32 object-cover rounded">
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('driver.documents.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="document_type" value="sim">
                        <input type="file" name="document" accept="image/*,application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            {{ $simDoc ? 'Ganti SIM' : 'Upload SIM' }}
                        </button>
                    </form>
                </div>

                <!-- STNK -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">STNK (Surat Tanda Nomor Kendaraan)</h3>
                    @php
                        $stnkDoc = $documents->where('document_type', 'stnk')->first();
                    @endphp
                    
                    @if($stnkDoc)
                        <div class="mb-2">
                            <span class="text-sm text-green-600">✓ Sudah diunggah</span>
                            <div class="mt-2">
                                <img src="{{ Storage::url($stnkDoc->document_path) }}" alt="STNK" class="max-w-full h-32 object-cover rounded">
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('driver.documents.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="document_type" value="stnk">
                        <input type="file" name="document" accept="image/*,application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            {{ $stnkDoc ? 'Ganti STNK' : 'Upload STNK' }}
                        </button>
                    </form>
                </div>

                <!-- Foto Profile -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Foto Profile</h3>
                    @php
                        $photoDoc = $documents->where('document_type', 'photo')->first();
                    @endphp
                    
                    @if($photoDoc)
                        <div class="mb-2">
                            <span class="text-sm text-green-600">✓ Sudah diunggah</span>
                            <div class="mt-2">
                                <img src="{{ Storage::url($photoDoc->document_path) }}" alt="Photo" class="max-w-full h-32 object-cover rounded">
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('driver.documents.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="document_type" value="photo">
                        <input type="file" name="document" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            {{ $photoDoc ? 'Ganti Foto' : 'Upload Foto' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('driver.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
