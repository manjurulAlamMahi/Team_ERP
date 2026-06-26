<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientMessageTypeStoreRequest;
use App\Http\Requests\ClientMessageTypeUpdateRequest;
use App\Models\ClientMessageType;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class ClientMessageTypeController extends Controller
{
    use AjaxResponse;

    public function list()
    {
        Gate::authorize('client_message_type_list');

        $data['types'] = ClientMessageType::withCount('messages')->latest()->get();

        return view('admin.pages.client-message-type.list', $data);
    }

    public function create()
    {
        Gate::authorize('client_message_type_create');

        return view('admin.pages.client-message-type.create');
    }

    public function store(ClientMessageTypeStoreRequest $request)
    {
        $data = $request->only(['name', 'short_description', 'format', 'restriction', 'mandatory', 'status']);

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->storeIcon($request->file('icon'));
        }

        ClientMessageType::create($data);

        return redirect()->route('client.message.type.list')->with('success', 'Client message type created successfully.');
    }

    public function edit($id)
    {
        Gate::authorize('client_message_type_edit');

        $data['type'] = ClientMessageType::findOrFail($id);

        return view('admin.pages.client-message-type.edit', $data);
    }

    public function update(ClientMessageTypeUpdateRequest $request)
    {
        $type = ClientMessageType::findOrFail($request->id);
        $data = $request->only(['name', 'short_description', 'format', 'restriction', 'mandatory', 'status']);

        if ($request->hasFile('icon')) {
            if ($type->icon && file_exists(public_path($type->icon))) {
                unlink(public_path($type->icon));
            }
            $data['icon'] = $this->storeIcon($request->file('icon'));
        }

        $type->update($data);

        return redirect()->route('client.message.type.list')->with('success', 'Client message type updated successfully.');
    }

    public function status(Request $request)
    {
        $type = ClientMessageType::find($request->id);

        if (!$type) {
            return $this->error([], 'Client message type not found', 404);
        }

        $type->update([
            'status' => $type->status === 'active' ? 'inactive' : 'active',
        ]);

        return $this->success($type, 'Status updated successfully', 200);
    }

    public function destroy(Request $request)
    {
        Gate::authorize('client_message_type_delete');

        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $type = ClientMessageType::find($request->id);

        if (!$type) {
            return $this->error([], 'Client message type not found', 404);
        }

        $type->delete();

        return $this->success([], 'Client message type deleted successfully', 200);
    }

    private function storeIcon($icon): string
    {
        $directory = 'client-message-types/icons';
        $filename = time() . '_' . uniqid() . '.' . $icon->getClientOriginalExtension();
        $icon->move(public_path($directory), $filename);

        return $directory . '/' . $filename;
    }
}
