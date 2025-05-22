<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $users = User::with('project')->get();
        return view('userconfig.index', compact('users'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('userconfig.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,officer,pm,vpqHSE',
            'project_id' => 'nullable|exists:projects,id',
            'pin' => 'nullable|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Jika role officer atau pm wajib ada project dan pin
        if (in_array($request->role, ['officer', 'pm', 'vpqHSE'])) {
            if (!$request->project_id) {
                return back()->withErrors(['project_id' => 'Project wajib diisi untuk role ini'])->withInput();
            }
            if (!$request->pin) {
                return back()->withErrors(['pin' => 'PIN wajib diisi untuk role ini'])->withInput();
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'project_id' => $request->project_id,
            'pin' => $request->pin,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('userconfig.index')->with('success', 'User berhasil dibuat');
    }
}
