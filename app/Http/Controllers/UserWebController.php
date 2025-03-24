<?php

namespace App\Http\Controllers;

use App\Domain\Services\UserWebService;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UserWebController extends Controller
{

    public function __construct(private UserWebService $userService) {}

    public function userActive()
    {
        $users = $this->userService->getActiveUsers();
        return view('pages.dashboards.users.user_active', compact('users'));
    }

    public function userNotActive()
    {
        $users = $this->userService->getInactiveUsers();
        return view('pages.dashboards.users.user_not_active', compact('users'));
    }

    public function admin()
    {
        $users = $this->userService->getAdmins();
        return view('pages.dashboards.admin.index', compact('users'));
    }

    public function addUser(UserRequest $request)
    {
        $this->userService->createUser($request->validated(), $request);
        return response()->json(['message' => 'تمت إضافة المسؤول بنجاح']);
    }

    public function changePasswordByAdmin(ChangePasswordRequest $request)
    {
        $validateDate = $request->validated();

        if ($this->userService->updateUserPassword($validateDate['user_id'], $validateDate['password'])) {
            return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
        }

        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }
}
