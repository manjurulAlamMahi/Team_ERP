<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    use AjaxResponse;

    public function getPermissionsForRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = $role->permissions;
        return response()->json(['permissions' => $permissions]);
    }

    public function assignPermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        // Validate that permissions exist and are valid
        $validatedPermissions = Permission::find($request->permissions);

        // Sync the permissions with the role
        $role->syncPermissions($validatedPermissions);

        return response()->json(['message' => 'Permissions updated successfully'], 200);
    }

    public function index()
    {
        Gate::authorize('setting_roleManagement');
        return view('admin.pages.role.index');
    }

    // Fetch all roles
    public function get()
    {
        $roles = Role::withCount('users')->where('name' , '!=' , 'Admin')->get();
        return response()->json($roles);
    }

    public function getPermissions()
    {
        $permission = Permission::get();
        return response()->json($permission);
    }

    // Create a new role
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
        ]);

        $role = Role::create(['name' => $request->name]);

        return response()->json($role);
    }

    // Create a new role
    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name|max:255',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json($permission);
    }

    // Update a role
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $request->id . '|max:255',
        ]);

        $role = Role::findOrFail($request->id);
        $role->name = $request->name;
        $role->save();

        return response()->json($role);
    }

    // Delete a role
    public function destroy(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password.', 500);
        }

        try {
            $role = Role::findOrFail($request->id);

            if ($role->name === 'Admin') {
                return $this->error([], 'The Admin role cannot be deleted.', 422);
            }

            DB::beginTransaction(); // Start DB Transaction

            // Get all users with this role before deleting it
            $users = User::role($role->name)->get();

            // Delete the role
            $role->delete();

            // Assign "Member" role to all affected users
            foreach ($users as $user) {
                $user->syncRoles(['Member']); // Ensure a baseline role is assigned
            }

            DB::commit(); // Commit changes

            return $this->success([], 'Successful.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], 'Something went wrong: ' . $e->getMessage(), 500);
        }
    }


    // Delete a role
    public function assignToUser(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect Password'
            ]);
        }
        $user = User::find($request->userId);
        $role = Role::find($request->role);
        $user->syncRoles($role); // Update the user's role

        // Sync the permissions for the user
        $user->syncPermissions($request->permissions);
        return response()->json([
            'status'  => 'success',
            'message' => 'Role updated successfully'
        ]);
    }

    public function updatePermissions(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect Password'
            ]);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'assigned_permissions' => 'array'
        ]);
        $role = Role::findOrFail($request->role_id);


        $assignedPermissionIds = $request->assigned_permissions;
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $validAssignedPermissions = array_intersect($rolePermissions, $assignedPermissionIds);

        $user = User::findOrFail($request->user_id);

        // Sync permissions (remove those not in array, add new ones)
        $user->syncPermissions($validAssignedPermissions);
        return response()->json([
            'status'  => 'success',
            'message' => 'Permissions updated successfully'
        ]);
    }
}
