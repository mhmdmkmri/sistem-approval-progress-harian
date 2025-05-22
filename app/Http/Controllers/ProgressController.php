<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Tampilkan daftar progress
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role);

        // dd($role);
        // exit;

        if ($role === 'admin') {
            // Admin lihat semua progress
            $progresses = Progress::with(['user', 'project', 'histories.user'])->get();
        } elseif ($role === 'officer' || $role === 'pm') {
            // Officer dan PM hanya lihat progress sesuai project_id mereka
            $progresses = Progress::with(['user', 'project', 'histories.user'])
                ->where('project_id', $user->project_id)
                ->get();
        } elseif (in_array($role, ['vpqhse', 'vp_qhse'])) {
            // VP QHSE lihat semua progress yang status sudah di-approve PM (approved_pm) dan belum diapprove VP
            $progresses = Progress::with(['user', 'project', 'histories.user'])
                ->where('status', 'approved_pm')
                ->get();
        } else {
            // Role lain (jika ada) bisa ditangani default atau abort
            abort(403, 'Tidak memiliki akses melihat progress');
        }

        // dd($progresses);
        // exit;

        return view('progress.index', compact('progresses', 'user'));
    }

    // Form input progress baru (hanya officer)
    public function create()
    {
        $user = Auth::user();
        if ($user->role != 'officer') {
            abort(403, 'Hanya Officer dapat input progress');
        }
        return view('progress.create');
    }

    // Simpan progress baru (hanya officer)
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role != 'officer') {
            abort(403, 'Hanya Officer dapat input progress');
        }

        $request->validate([
            'date' => 'required|date',
            'progress_percent' => 'required|integer|min:0|max:100',
            'evidence' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('evidence', 'public');
        }

        Progress::create([
            'user_id' => $user->id,
            'project_id' => $user->project_id,
            'date' => $request->date,
            'progress_percent' => $request->progress_percent,
            'evidence_path' => $evidencePath,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('progress.index')->with('success', 'Progress berhasil disimpan');
    }

    // Approve progress oleh PM atau VP QHSE
    public function approve(Request $request, Progress $progress)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['pm', 'vp_qhse', 'vpqhse', 'vpqHSE'])) {
            abort(403, 'Tidak memiliki akses approval');
        }

        $request->validate([
            'pin' => 'required|string',
        ]);

        if ($request->pin !== $user->pin) {
            return back()->withErrors(['pin' => 'PIN salah']);
        }

        if ($user->role === 'pm') {
            // PM hanya bisa approve jika status masih pending
            if ($progress->status !== 'pending') {
                return back()->withErrors(['pin' => 'PM hanya bisa menyetujui progress yang masih pending']);
            }
            $progress->status = 'approved_pm';
        } elseif (in_array(strtolower($user->role), ['vp_qhse', 'vpqhse', 'vpqhse'])) {
            // VP QHSE hanya bisa approve jika sudah approved oleh PM
            if ($progress->status !== 'approved_pm') {
                return back()->withErrors(['pin' => 'VP QHSE hanya bisa menyetujui progress yang sudah disetujui PM']);
            }
            $progress->status = 'approved_vp';
        }

        $progress->save();

        // Simpan history approval
        $progress->histories()->create([
            'user_id' => $user->id,
            'action' => $progress->status,
            'status' => $progress->status,
            'note' => 'Disetujui',
            'action_at' => now(),
        ]);

        return redirect()->route('progress.index')->with('success', 'Progress berhasil disetujui');
    }

    // Reject progress oleh PM atau VP QHSE
    public function reject(Request $request, Progress $progress)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['pm', 'vp_qhse'])) {
            abort(403, 'Tidak memiliki akses rejection');
        }

        $request->validate([
            'pin' => 'required|string',
            'note' => 'required|string',
        ]);

        if ($request->pin !== $user->pin) {
            return back()->withErrors(['pin' => 'PIN salah']);
        }

        // Validasi status saat reject
        if ($user->role === 'pm' && $progress->status !== 'pending') {
            return back()->withErrors(['pin' => 'PM hanya bisa menolak progress yang masih pending']);
        }

        if ($user->role === 'vp_qhse' && $progress->status !== 'approved_pm') {
            return back()->withErrors(['pin' => 'VP QHSE hanya bisa menolak progress yang sudah di-approve oleh PM']);
        }

        $progress->status = 'rejected';
        $progress->save();

        // Simpan history rejection dengan catatan
        $progress->histories()->create([
            'user_id' => $user->id,
            'action' => 'rejected',
            'comment' => $request->note,
            'action_at' => now(),
        ]);

        return redirect()->route('progress.index')->with('success', 'Progress berhasil ditolak');
    }

    // Tampilkan detail progress
    public function show($id)
    {
        $progress = Progress::with('user', 'project')->findOrFail($id);
        return view('progress.show', compact('progress'));
    }
}
