<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;

class DashboardController extends Controller
{
    public function index()
    {
        $statusCounts = Progress::select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $allStatuses = ['pending', 'approved_pm', 'approved_vp', 'rejected'];

        $data = [];
        foreach ($allStatuses as $status) {
            $data[$status] = $statusCounts[$status] ?? 0;
        }

        return view('dashboard', ['statusData' => $data]);
    }

    // Method untuk API data progress berdasarkan status
    public function getProgressByStatus(Request $request)
    {
        $status = $request->query('status');

        $validStatuses = ['pending', 'approved_pm', 'approved_vp', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        try {
            $progresses = Progress::with(['user', 'project'])
                ->where('status', $status)
                ->get();

            return response()->json($progresses);
        } catch (\Exception $e) {
            \Log::error('Error getProgressByStatus: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    // Method baru untuk API data semua progress
    public function getAllProgress()
    {
        try {
            $progresses = Progress::with(['user', 'project'])->get();
            return response()->json($progresses);
        } catch (\Exception $e) {
            \Log::error('Error getAllProgress: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
