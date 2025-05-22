@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-semibold mb-8 text-center text-gray-800">Input Progress</h1>

    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded border border-red-300">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('progress.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label for="date" class="block mb-2 font-medium text-gray-700">Tanggal:</label>
            <input
                type="date"
                id="date"
                name="date"
                value="{{ date('Y-m-d') }}"
                required
                class="w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            >
        </div>

        <div>
            <label for="progress_percent" class="block mb-2 font-medium text-gray-700">Progress (%):</label>
            <input
                type="number"
                id="progress_percent"
                name="progress_percent"
                min="0" max="100"
                value="{{ old('progress_percent') }}"
                required
                class="w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            >
        </div>

        <div>
            <label for="evidence" class="block mb-2 font-medium text-gray-700">Bukti (foto):</label>
            <input
                type="file"
                id="evidence"
                name="evidence"
                accept="image/*"
                class="w-full text-gray-700"
            >
        </div>

        <div>
            <label for="description" class="block mb-2 font-medium text-gray-700">Keterangan:</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                class="w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            >{{ old('description') }}</textarea>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-3 rounded-md hover:bg-blue-700 transition"
        >
            Simpan
        </button>
    </form>
</div>
@endsection
