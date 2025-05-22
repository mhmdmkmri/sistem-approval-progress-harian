@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📋 Daftar Progress</h1>

        @if($user->role == 'officer')
        <a href="{{ route('progress.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Tambah Progress
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md border border-green-300">
        {{ session('success') }}
    </div>
    @endif

    @error('pin')
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md border border-red-300">
        {{ $message }}
    </div>
    @enderror

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($progresses as $p)
        <div class="bg-white shadow-md border border-gray-200 rounded-xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">{{ $p->project->name }}</h2>
                <span class="text-sm text-gray-500">{{ $p->date?->format('d M Y') ?? '-' }}</span>
            </div>

            <p class="text-sm text-gray-600">Officer: <strong>{{ $p->user->name }}</strong></p>

            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-700">Progress:</span>
                <span class="text-lg font-bold text-blue-600">{{ $p->progress_percent }}%</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-700">Status:</span>
                @php
                $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'approved_pm' => 'bg-blue-100 text-blue-800',
                'approved_vp' => 'bg-green-100 text-green-700',
                'rejected' => 'bg-red-100 text-red-700',
                ];
                $statusLabels = [
                'pending' => 'Menunggu',
                'approved_pm' => 'Disetujui PM',
                'approved_vp' => 'Disetujui VP',
                'rejected' => 'Ditolak',
                ];
                @endphp
                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded {{ $statusColors[$p->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $statusLabels[$p->status] ?? ucfirst($p->status) }}
                </span>
            </div>

            @php
            $rejectHistory = $p->histories
            ->where('action', 'rejected')
            ->sortByDesc('action_at')
            ->first();
            @endphp

            @if($p->status === 'rejected' && $rejectHistory && !empty($rejectHistory->comment))
            <div class="mt-2 flex justify-between items-center">
                <span class="text-sm font-semibold text-red-600">Catatan Reject:</span>
                <span class="text-sm text-red-700 italic max-w-[70%] text-right break-words">{{ $rejectHistory->comment }}</span>
            </div>

            @endif



            <div>
                <span class="text-sm text-gray-700">Bukti:</span>
                @if($p->evidence_path)
                <a href="{{ asset('storage/' . $p->evidence_path) }}"
                    target="_blank"
                    class="text-blue-600 text-sm underline ml-1">Lihat Gambar</a>
                @else
                <span class="text-sm text-gray-400">Tidak ada</span>
                @endif
            </div>

            <div class="mt-3">
                <p class="text-sm text-gray-700 mb-1">QR Code:</p>
                <div class="bg-white border rounded p-2 inline-block">
                    {!! QrCode::size(80)->generate(route('progress.show', $p->id)) !!}
                </div>
            </div>

            {{-- Approval untuk PM (jika status pending & proyek cocok) --}}
            @if($user->role === 'pm' && $p->status === 'pending' && $p->project_id === $user->project_id)
            <form action="{{ route('progress.approve', $p->id) }}" method="POST" class="space-y-2">
                @csrf
                <div class="flex flex-col gap-2">
                    <input type="password" name="pin" placeholder="PIN" maxlength="6" required
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300" />

                    <button type="submit"
                        class="w-full inline-block px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                        ✅ Approve
                    </button>
                </div>
            </form>

            <form action="{{ route('progress.reject', $p->id) }}" method="POST" class="space-y-2 mt-2">
                @csrf
                <div class="flex flex-col gap-2">
                    <input type="password" name="pin" placeholder="PIN" maxlength="6" required
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-red-300" />
                    <input type="text" name="note" placeholder="Catatan (wajib)" required
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-red-300" />

                    <button type="submit"
                        class="w-full inline-block px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm">
                        ❌ Reject
                    </button>
                </div>
            </form>
            @endif

            {{-- Approval untuk VP QHSE (jika status approved_pm) --}}
            @if(in_array($user->role, ['vp_qhse', 'vpqhse', 'vpqHSE']) && $p->status === 'approved_pm')
            <form action="{{ route('progress.approve', $p->id) }}" method="POST" class="space-y-2">
                @csrf
                <div class="flex flex-col gap-2">
                    <input type="password" name="pin" placeholder="PIN" maxlength="6" required
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300" />

                    <button type="submit"
                        class="w-full inline-block px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                        ✅ Approve
                    </button>
                </div>
            </form>

            <form action="{{ route('progress.reject', $p->id) }}" method="POST" class="space-y-2 mt-2">
                @csrf
                <div class="flex flex-col gap-2">
                    <input type="password" name="pin" placeholder="PIN" maxlength="6" required
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-red-300" />
                    <input type="text" name="note" placeholder="Catatan (wajib)" required
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-red-300" />

                    <button type="submit"
                        class="w-full inline-block px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm">
                        ❌ Reject
                    </button>
                </div>
            </form>
            @endif

        </div>
        @empty
        <div class="col-span-full text-center text-gray-500">Belum ada data progress.</div>
        @endforelse
    </div>
</div>
@endsection