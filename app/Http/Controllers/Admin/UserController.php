<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Chat;
use App\Models\Community;
use App\Models\Stack;
use App\Models\Team;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AjaxResponse;

    public function create()
    {
        Gate::authorize('user_create');

        $data['roles'] = Role::where('name', '!=', 'Admin')->get();
        $data['communities'] = Community::where('status', 'active')->get();
        $data['teams'] = Team::where('status', 'active')->get();
        $data['stacks'] = Stack::where('status', 'active')->get();
        $data['managers'] = User::role('Operation Manager')->orderBy('name')->get();

        return view('admin.pages.user.create', $data);
    }

    public function store(UserStoreRequest $request)
    {
        $employeeId = $request->employee_id;

        if (!$employeeId || !preg_match('/^EMP-\d{4}$/', $employeeId)) {
            $lastEmployeeId = User::withTrashed()->orderByDesc('id')->value('employee_id');
            $nextNumber = $lastEmployeeId ? ((int) substr($lastEmployeeId, 4)) + 1 : 1;
            $employeeId = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        $teamId = $request->team_id;
        $communityId = $request->community_id;

        $teamRoles = ['Leader', 'Co Leader', 'Stack Lead', 'Member', 'Probation'];
        if (in_array($request->role, $teamRoles, true)) {
            $team = Team::findOrFail($teamId);
            $communityId = $team->community_id;
        } elseif ($request->role === 'Operation Manager') {
            $teamId = null;
        } else {
            $teamId = null;
            $communityId = null;
        }

        $user = User::create([
            'employee_id' => $employeeId,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'official_email' => $request->official_email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'telegram' => $request->telegram,
            'github' => $request->github,
            'discord' => $request->discord,
            'address' => $request->address,
            'team_id' => $teamId,
            'community_id' => $communityId,
            'stack_id' => $request->stack_id,
            'joining_date' => $request->joining_date,
            'probation_end_date' => $request->probation_end_date,
            'reporting_to' => $request->reporting_to,
            'added_by' => Auth::id(),
            'is_request' => false,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        $user->sendEmailVerificationNotification();

        return redirect()->route('user.list')->with('success', 'User created successfully.');
    }

    public function list()
    {
        Gate::authorize('user_list');

        $data['users'] = User::with(['team', 'stack', 'roles'])
            ->where('id', '!=', Auth::id())
            ->where('is_admin', false)
            ->where('is_request', false)
            ->get();

        return view('admin.pages.user.list', $data);
    }

    public function request()
    {
        Gate::authorize('user_request');
        $data['users'] = User::where('is_request', true)->get();
        return view('admin.pages.user.request', $data);
    }

    public function update(UserUpdateRequest $request)
    {
        Gate::authorize('user_edit');

        $user = User::findOrFail($request->id);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('profile_update', true)
                ->withErrors(['password' => 'The password you entered does not match your current password.'])
                ->withInput();
        }

        $teamId = $request->team_id;
        $communityId = $request->community_id;

        $teamRoles = ['Leader', 'Co Leader', 'Stack Lead', 'Member', 'Probation'];
        if (in_array($request->role, $teamRoles, true)) {
            $team = Team::findOrFail($teamId);
            $communityId = $team->community_id;
        } elseif ($request->role === 'Operation Manager') {
            $teamId = null;
        } else {
            $teamId = null;
            $communityId = null;
        }

        $user->update([
            'employee_id' => $request->employee_id,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'official_email' => $request->official_email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'telegram' => $request->telegram,
            'github' => $request->github,
            'discord' => $request->discord,
            'address' => $request->address,
            'team_id' => $teamId,
            'community_id' => $communityId,
            'stack_id' => $request->stack_id,
            'joining_date' => $request->joining_date,
            'probation_end_date' => $request->probation_end_date,
            'reporting_to' => $request->reporting_to,
        ]);

        $user->syncRoles([$request->role]);

        if ($request->filled('newpassword')) {
            $user->password = bcrypt($request->newpassword);
            $user->save();
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return redirect()->route('user.list')->with('success', 'User updated successfully.');
    }

    public function status(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);

        if ($user->status == 'inactive') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return $this->success($user, 'User Status Updated Successfully', 200);
    }

    public function profile($username)
    {
        $data['username'] = $username;
        $data['user'] = User::where('username', $username)->first();
        return view('admin.pages.user.profile', $data);
    }

    public function accept($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $user->update([
            'is_request' => false
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()->route('user.request')->with('success', 'Successful.');
    }

    public function edit($username)
    {
        Gate::authorize('user_edit');

        $data['username'] = $username;
        $data['user'] = User::where('username', $username)->first();
        $data['roles'] = Role::all();
        $data['communities'] = Community::where('status', 'active')->get();
        $data['teams'] = Team::where('status', 'active')->get();
        $data['stacks'] = Stack::where('status', 'active')->get();
        $data['managers'] = User::role('Operation Manager')->where('id', '!=', $data['user']->id)->orderBy('name')->get();

        return view('admin.pages.user.edit', $data);
    }

    public function destroy(Request $request)
    {
        Gate::authorize('user_delete');

        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $user = User::find($request->id);
        $user->syncRoles(['Member']);
        $deleted = $user->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        if ($deleted) {
            $admins = User::where('is_admin', true)->get();
            Notification::send($admins, new AdminNotification('User Removed', 'User ' . $user->name . ' has been removed by ' . Auth::user()->name, 'danger', 'ri-delete-bin-7-line'));
            return $this->success([], 'Account Deleted Successfully', 200);
        }

        return $this->error([], 'Account deletion failed. Please try again.', 500);
    }

    public function redirect_assignrole(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $url = route('user.assignrole', $request->id);

        return $this->success($url, 'Success.', 200);
    }

    public function assignrole($id)
    {
        $data['user']  = User::find($id);
        $data['roles'] = Role::where('name' , '!=' , 'Admin')->get();

        return view('admin.pages.user.assignrole', $data);
    }

    public function message_store(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $receiverId = $request->id;
        $authUserId = Auth::id();

        $conversationId = implode('-', [min($authUserId, $receiverId), max($authUserId, $receiverId)]);

        $chat = Chat::create([
            'sender_id'       => $authUserId,
            'receiver_id'     => $receiverId,
            'message'         => "Hello " . $user->name,
            'conversation_id' => $conversationId,
        ]);

        broadcast(new ChatMessageSent($chat))->toOthers();

        return redirect()->route('dashboard.inbox')->with('success', 'Message sent successfully');
    }
}
