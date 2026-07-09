<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\FiverrProfile;
use App\Models\User;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    use AjaxResponse;

    private function currentTeamUser(): User
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user instanceof User && $user->team_id, 403);

        return $user;
    }

    /**
     * Only Leader, Co Leader, Stack Lead may create, edit, delete or assign clients.
     */
    private function leadUser(): User
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']), 403);

        return $user;
    }

    public function createForm()
    {
        $this->leadUser();

        $countries = config('countries');
        $profiles = FiverrProfile::where('status', 'active')->orderBy('name')->get();

        return view('admin.pages.client.create', compact('countries', 'profiles'));
    }

    public function store(StoreClientRequest $request)
    {
        $user = $this->leadUser();

        Client::create([
            'team_id' => $user->team_id,
            'created_by' => $user->id,
            'username' => $request->username,
            'profile_id' => $request->profile_id,
            'client_name' => $request->client_name,
            'country' => $request->country,
            'sales_man_name' => $request->sales_man_name,
            'sales_man_whatsapp' => $request->sales_man_whatsapp,
        ]);

        return redirect()->route('client.list')->with('success', 'Client created successfully.');
    }

    public function edit($id)
    {
        $user = $this->leadUser();

        $client = Client::forTeam($user->team_id)->findOrFail($id);
        abort_unless($client->isEditableBy($user), 403);

        $countries = config('countries');
        $profiles = FiverrProfile::where('status', 'active')->orderBy('name')->get();

        return view('admin.pages.client.edit', compact('client', 'countries', 'profiles'));
    }

    public function update(UpdateClientRequest $request)
    {
        $user = $this->leadUser();

        $client = Client::forTeam($user->team_id)->findOrFail($request->id);
        abort_unless($client->isEditableBy($user), 403);

        $client->update([
            'username' => $request->username,
            'profile_id' => $request->profile_id,
            'client_name' => $request->client_name,
            'country' => $request->country,
            'sales_man_name' => $request->sales_man_name,
            'sales_man_whatsapp' => $request->sales_man_whatsapp,
        ]);

        return redirect()->route('client.list')->with('success', 'Client updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $this->currentTeamUser();

        $client = Client::forTeam($user->team_id)->find($request->id);

        if (!$client) {
            return $this->error([], 'Client not found', 404);
        }

        if (!$client->isDeletableBy($user)) {
            return $this->error([], 'You are not allowed to delete this client.', 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $client->delete();

        return $this->success([], 'Client deleted successfully', 200);
    }

    public function list(Request $request)
    {
        $user = $this->currentTeamUser();

        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();
        $profiles = FiverrProfile::where('status', 'active')->orderBy('name')->get();

        return view('admin.pages.client.list', compact('clients', 'profiles'));
    }

    public function myClients()
    {
        $user = $this->currentTeamUser();

        $clients = Client::with('profile')
            ->forTeam($user->team_id)
            ->whereHas('assignees', fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('username')
            ->get();
        $profiles = FiverrProfile::where('status', 'active')->orderBy('name')->get();

        return view('admin.pages.client.my-clients', compact('clients', 'profiles'));
    }

    public function assignees($id)
    {
        $user = $this->leadUser();

        $client = Client::forTeam($user->team_id)->with('assignees')->findOrFail($id);

        $members = User::where('team_id', $user->team_id)->orderBy('name')->get(['id', 'name']);
        $assignedIds = $client->assignees->pluck('id');

        return $this->success([
            'client_id' => $client->id,
            'members' => $members,
            'assigned_ids' => $assignedIds,
        ], 'ok', 200);
    }

    public function storeAssign(Request $request)
    {
        $user = $this->leadUser();

        $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'assignee_ids' => ['array'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $client = Client::forTeam($user->team_id)->find($request->client_id);

        if (!$client) {
            return $this->error([], 'Client not found', 404);
        }

        $ids = $request->input('assignee_ids', []);

        if (!empty($ids) && User::whereIn('id', $ids)->where('team_id', $user->team_id)->count() !== count($ids)) {
            return $this->error([], 'All assignees must belong to your team.', 422);
        }

        $client->assignees()->sync(
            collect($ids)->mapWithKeys(fn ($assigneeId) => [$assigneeId => ['assigned_by' => $user->id]])
        );

        return $this->success($client->load('assignees'), 'Client assignees updated successfully', 200);
    }
}
