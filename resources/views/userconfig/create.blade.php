@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Tambah User Baru</h1>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('userconfig.store') }}" method="POST" class="space-y-5 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block font-medium mb-1">Nama:</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Role:</label>
            <select name="role" id="role" required class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="officer" {{ old('role') == 'officer' ? 'selected' : '' }}>Officer</option>
                <option value="pm" {{ old('role') == 'pm' ? 'selected' : '' }}>PM</option>
                <option value="vpqHSE" {{ old('role') == 'vpqHSE' ? 'selected' : '' }}>VP QHSE</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Project:</label>
            <select name="project_id" id="project_id" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- Pilih Project --</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">PIN (6 digit):</label>
            <input type="text" name="pin" maxlength="6" value="{{ old('pin') }}" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Password:</label>
            <input type="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Konfirmasi Password:</label>
            <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
    // Wajib isi Project dan PIN jika role tertentu
    document.getElementById('role').addEventListener('change', function() {
        let role = this.value;
        let projectInput = document.getElementById('project_id');
        let pinInput = document.querySelector('input[name="pin"]');

        if (['officer', 'pm', 'vpqHSE'].includes(role)) {
            projectInput.required = true;
            pinInput.required = true;
        } else {
            projectInput.required = false;
            pinInput.required = false;
        }
    });
</script>
@endsection