<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;

class UserApiController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'project_id')->get();
        return $this->apiSuccess($users, 'Data users retrieved');
    }
}
