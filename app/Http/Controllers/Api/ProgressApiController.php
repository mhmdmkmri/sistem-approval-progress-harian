<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressApiController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            $progresses = Progress::with('user', 'project')->get();
        } else {
            $progresses = Progress::with('user', 'project')->where('project_id', $user->project_id)->get();
        }

        return $this->apiSuccess($progresses, 'Data progress retrieved');
    }
}
