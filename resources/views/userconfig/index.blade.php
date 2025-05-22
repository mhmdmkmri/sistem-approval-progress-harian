@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">User Configuration</h1>

        @if(session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <a href="{{ route('userconfig.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Tambah User
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">Nama</th>
                        <th class="border px-4 py-2 text-left">Email</th>
                        <th class="border px-4 py-2 text-left">Role</th>
                        <th class="border px-4 py-2 text-left">Proyek</th>
                        <th class="border px-4 py-2 text-left">PIN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2">{{ $u->name }}</td>
                            <td class="border px-4 py-2">{{ $u->email }}</td>
                            <td class="border px-4 py-2">{{ strtoupper($u->role) }}</td>
                            <td class="border px-4 py-2">{{ $u->project ? $u->project->name : '-' }}</td>
                            <td class="border px-4 py-2">{{ $u->pin ? '******' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
