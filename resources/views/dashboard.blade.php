@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 max-w-6xl">

    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-left">All Progress Details</h2>

    <div class="bg-white overflow-auto shadow-sm rounded-lg p-4 max-h-[300px] mb-12 border border-gray-300">
        <table class="w-full text-left border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-2 py-1">ID</th>
                    <th class="border border-gray-300 px-2 py-1">User</th>
                    <th class="border border-gray-300 px-2 py-1">Project</th>
                    <th class="border border-gray-300 px-2 py-1">Date</th>
                    <th class="border border-gray-300 px-2 py-1">Progress (%)</th>
                    <th class="border border-gray-300 px-2 py-1">Description</th>
                    <th class="border border-gray-300 px-2 py-1">Status</th>
                </tr>
            </thead>
            <tbody id="allProgressTableBody">
                <tr><td colspan="7" class="text-center py-4">Loading...</td></tr>
            </tbody>
        </table>
    </div>

    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-left">Dashboard</h2>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mx-auto flex gap-8 max-w-full">
        <!-- Chart container -->
        <div class="max-w-md flex-shrink-0">
            <canvas id="progressPieChart"></canvas>
        </div>

        <!-- Status summary table -->
        <div class="flex-1">
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                        <th class="border border-gray-300 px-4 py-2">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabels = ['pending' => 'Pending', 'approved_pm' => 'Approved PM', 'approved_vp' => 'Approved VP', 'rejected' => 'Rejected'];
                        $statusColors = ['pending' => '#f59e0b', 'approved_pm' => '#10b981', 'approved_vp' => '#3b82f6', 'rejected' => '#ef4444'];
                    @endphp
                    @foreach($statusLabels as $key => $label)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">
                            <span 
                                class="inline-block px-3 py-1 rounded text-white font-semibold"
                                style="background-color: {{ $statusColors[$key] }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            {{ $statusData[$key] ?? 0 }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal popup hidden by default -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg max-w-3xl w-full p-6 overflow-auto max-h-[80vh]">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Detail Progress</h3>
            <button id="closeModalBtn" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
        </div>

        <table class="w-full text-left border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-2 py-1">ID</th>
                    <th class="border border-gray-300 px-2 py-1">User</th>
                    <th class="border border-gray-300 px-2 py-1">Project</th>
                    <th class="border border-gray-300 px-2 py-1">Date</th>
                    <th class="border border-gray-300 px-2 py-1">Progress (%)</th>
                    <th class="border border-gray-300 px-2 py-1">Description</th>
                </tr>
            </thead>
            <tbody id="modalTableBody">
                <!-- Data akan di-inject JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('progressPieChart').getContext('2d');
        const statusData = @json($statusData);

        const labels = ['Pending', 'Approved PM', 'Approved VP', 'Rejected'];
        const statusKeys = ['pending', 'approved_pm', 'approved_vp', 'rejected'];
        const colors = ['#f59e0b', '#10b981', '#3b82f6', '#ef4444'];

        const data = {
            labels: labels,
            datasets: [{
                label: 'Progress Status',
                data: Object.values(statusData),
                backgroundColor: colors,
                hoverOffset: 30
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Progress Status Distribution'
                    }
                },
                onClick(evt, activeEls) {
                    if (activeEls.length > 0) {
                        const idx = activeEls[0].index;
                        const clickedStatusKey = statusKeys[idx];
                        openDetailModal(clickedStatusKey);
                    }
                }
            }
        };

        const pieChart = new Chart(ctx, config);

        // Modal elements
        const modal = document.getElementById('detailModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalTableBody = document.getElementById('modalTableBody');
        const allProgressTableBody = document.getElementById('allProgressTableBody');

        closeModalBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modalTableBody.innerHTML = '';
        });

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${dd}-${mm}-${yyyy}`;
        }

        async function openDetailModal(status) {
            modalTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>';
            modal.classList.remove('hidden');

            try {
                const response = await fetch(`/api/progress-by-status?status=${status}`);
                if (!response.ok) throw new Error('Failed to fetch data');
                const progresses = await response.json();

                if (progresses.length === 0) {
                    modalTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No data found</td></tr>';
                    return;
                }

                modalTableBody.innerHTML = progresses.map(p => `
                    <tr>
                        <td class="border border-gray-300 px-2 py-1">${p.id}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.user ? p.user.name : '-'}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.project ? p.project.name : '-'}</td>
                        <td class="border border-gray-300 px-2 py-1">${formatDate(p.date)}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.progress_percent}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.description ?? ''}</td>
                    </tr>
                `).join('');

            } catch (err) {
                modalTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-red-500 py-4">Error loading data</td></tr>`;
                console.error(err);
            }
        }

        // Load all data for the top full table on page load
        async function loadAllProgressData() {
            try {
                const response = await fetch('/api/progress-all');
                if (!response.ok) throw new Error('Failed to fetch all progress data');
                const allProgress = await response.json();

                if (allProgress.length === 0) {
                    allProgressTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No data found</td></tr>';
                    return;
                }

                allProgressTableBody.innerHTML = allProgress.map(p => `
                    <tr>
                        <td class="border border-gray-300 px-2 py-1">${p.id}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.user ? p.user.name : '-'}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.project ? p.project.name : '-'}</td>
                        <td class="border border-gray-300 px-2 py-1">${formatDate(p.date)}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.progress_percent}</td>
                        <td class="border border-gray-300 px-2 py-1">${p.description ?? ''}</td>
                        <td class="border border-gray-300 px-2 py-1 capitalize">${p.status ?? '-'}</td>
                    </tr>
                `).join('');

            } catch (err) {
                allProgressTableBody.innerHTML = `<tr><td colspan="7" class="text-center text-red-500 py-4">Error loading data</td></tr>`;
                console.error(err);
            }
        }

        loadAllProgressData();

    });
</script>
@endsection
