<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\Role;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        try {

            $data = $this->userService->getAllUsers($request);

            return view('Users.index', $data);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getUserRoles(Request $request)
    {
        try {

            $roles = $this->userService->getUserRoles($request);
            $permissions = $this->userService->getPermissions();
            return view(
                'Users.user_roles',
                compact('roles', 'permissions')
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function storeUserRoles(Request $request)
    {
        try {
           $store = $this->userService->storeUserRoles($request);
            return redirect()->route('users.role')->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUserRoles(Request $request, Role $role)
    {
        try {
            $update = $this->userService->updateUserRoles($request, $role);
              return response()->json([
                'message' => 'Role updated successfully',
                'role' => $update,
                'redirect' => route('users.role'),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
