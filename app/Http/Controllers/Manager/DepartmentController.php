<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function index()
    {
        $loggedInUser = auth()->user();
        $departments = [];

        switch ($loggedInUser->role_as) {
            case User::ROLE_IT_MANAGER:
                $departments = ['IT', 'Website Development'];
                break;
            case User::ROLE_MARKETING_MANAGER:
                $departments = ['Marketing'];
                break;
            case User::ROLE_OPERATIONS_MANAGER:
                $departments = ['SEO', 'Content'];
                break;
            default:
                // Handle other roles or return an error
                return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::getUsersByDepartments($departments);

           
        return view ('manager.department', compact('users','departments'));
    }

    public function edit($user_id)
    {
        $user = User::findOrFail($user_id);

        $department = $user->department;
        $supervisor = User::getSupervisorForDepartment($department, $user);

        return view('manager.record', compact('user','supervisor'));
    }
}
