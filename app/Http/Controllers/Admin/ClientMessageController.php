<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientMessage;
use App\Models\ClientMessageAttachment;
use App\Models\ClientMessageType;
use App\Models\Team;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class ClientMessageController extends Controller
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
     * Only Stack Lead, Member, Probation submit messages for approval.
     */
    private function submitterUser(): User
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasAnyRole(['Stack Lead', 'Member', 'Probation']), 403);

        return $user;
    }

    /**
     * Only the team Leader/Co Leader may review submissions.
     */
    private function reviewerTeam(): Team
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasAnyRole(['Leader', 'Co Leader']), 403);

        return Team::findOrFail($user->team_id);
    }

    public function createForm(Request $request)
    {
        $this->submitterUser();

        $types = ClientMessageType::active()->orderBy('name')->get();
        $selectedType = $request->filled('type') ? ClientMessageType::active()->find($request->query('type')) : null;

        return view('admin.pages.client-message.create', compact('types', 'selectedType'));
    }

    public function store(Request $request)
    {
        $user = $this->submitterUser();
        $team = Team::findOrFail($user->team_id);

        $this->validatePayload($request);

        $type = ClientMessageType::active()->find($request->client_message_type_id);
        if (!$type) {
            return back()->withErrors(['client_message_type_id' => 'Selected message type is invalid.'])->withInput();
        }

        $message = ClientMessage::create([
            'team_id' => $team->id,
            'client_message_type_id' => $type->id,
            'submitted_by' => $user->id,
            'client_name' => $request->client_name,
            'profile_name' => $request->profile_name,
            'last_message_type' => $request->last_message_type,
            'their_message' => $request->their_message,
            'status' => 'pending',
        ]);

        $this->storeAttachments($message, $request->file('last_message_files', []), 'last_message');
        $this->storeAttachments($message, $request->file('attachment_files', []), 'attachment');

        $this->notifyReviewers($team, $user, $message, $type);

        return redirect()->route('client.message.my.list')->with('success', 'Message submitted for approval.');
    }

    public function myList()
    {
        $user = $this->submitterUser();

        $messages = ClientMessage::with('type')
            ->where('submitted_by', $user->id)
            ->latest()
            ->get();

        return view('admin.pages.client-message.my-list', compact('messages'));
    }

    public function myShow($id)
    {
        $user = $this->submitterUser();

        $message = ClientMessage::with(['type', 'attachments', 'reviewer', 'submitter.stack'])
            ->where('submitted_by', $user->id)
            ->findOrFail($id);

        return view('admin.pages.client-message.my-show', ['clientMessage' => $message]);
    }

    public function edit($id)
    {
        $user = $this->submitterUser();

        $message = ClientMessage::with('attachments')
            ->where('submitted_by', $user->id)
            ->findOrFail($id);

        abort_unless($message->isEditableBy($user), 403);

        $types = ClientMessageType::active()->orderBy('name')->get();

        return view('admin.pages.client-message.edit', ['clientMessage' => $message, 'types' => $types]);
    }

    public function update(Request $request)
    {
        $user = $this->submitterUser();

        $message = ClientMessage::where('submitted_by', $user->id)->findOrFail($request->id);
        abort_unless($message->isEditableBy($user), 403);

        $this->validatePayload($request);

        $type = ClientMessageType::active()->find($request->client_message_type_id);
        if (!$type) {
            return back()->withErrors(['client_message_type_id' => 'Selected message type is invalid.'])->withInput();
        }

        $message->update([
            'client_message_type_id' => $type->id,
            'client_name' => $request->client_name,
            'profile_name' => $request->profile_name,
            'last_message_type' => $request->last_message_type,
            'their_message' => $request->their_message,
        ]);

        if ($request->hasFile('last_message_files')) {
            $this->deleteAttachments($message, 'last_message');
            $this->storeAttachments($message, $request->file('last_message_files', []), 'last_message');
        }

        if ($request->hasFile('attachment_files')) {
            $this->deleteAttachments($message, 'attachment');
            $this->storeAttachments($message, $request->file('attachment_files', []), 'attachment');
        }

        return redirect()->route('client.message.my.list')->with('success', 'Message updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $this->submitterUser();

        $message = ClientMessage::where('submitted_by', $user->id)->find($request->id);

        if (!$message) {
            return $this->error([], 'Message not found', 404);
        }

        if (!$message->isEditableBy($user)) {
            return $this->error([], 'This message can no longer be deleted.', 403);
        }

        $this->deleteAttachments($message, 'last_message');
        $this->deleteAttachments($message, 'attachment');
        $message->delete();

        return $this->success([], 'Message deleted successfully', 200);
    }

    public function reviewList()
    {
        $team = $this->reviewerTeam();

        $messages = ClientMessage::with(['type', 'submitter'])
            ->forTeam($team->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.pages.client-message.review-list', compact('team', 'messages'));
    }

    public function reviewHistory()
    {
        $team = $this->reviewerTeam();

        $messages = ClientMessage::with(['type', 'submitter', 'reviewer'])
            ->forTeam($team->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->get();

        return view('admin.pages.client-message.review-history', compact('team', 'messages'));
    }

    public function reviewShow($id)
    {
        $team = $this->reviewerTeam();

        $message = ClientMessage::with(['type', 'submitter.stack', 'reviewer', 'attachments'])
            ->forTeam($team->id)
            ->findOrFail($id);

        return view('admin.pages.client-message.review-show', ['clientMessage' => $message]);
    }

    public function approve(Request $request)
    {
        $team = $this->reviewerTeam();

        $request->validate(['id' => 'required|exists:client_messages,id']);

        $message = ClientMessage::forTeam($team->id)->where('status', 'pending')->find($request->id);
        if (!$message) {
            return $this->error([], 'Message not found or already reviewed.', 404);
        }

        $message->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::send($message->submitter, new AdminNotification(
            'Client Message Approved',
            'Your "' . $message->type->name . '" message for ' . $message->client_name . ' was approved by ' . Auth::user()->name . '. You may now send it.',
            'success',
            'ri-checkbox-circle-line'
        ));

        return $this->success($message, 'Message approved successfully', 200);
    }

    public function reject(Request $request)
    {
        $team = $this->reviewerTeam();

        $request->validate([
            'id' => 'required|exists:client_messages,id',
            'reason' => 'required|string|max:1000',
        ]);

        $message = ClientMessage::forTeam($team->id)->where('status', 'pending')->find($request->id);
        if (!$message) {
            return $this->error([], 'Message not found or already reviewed.', 404);
        }

        $message->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::send($message->submitter, new AdminNotification(
            'Client Message Rejected',
            'Your "' . $message->type->name . '" message for ' . $message->client_name . ' was rejected by ' . Auth::user()->name . '. Reason: ' . $request->reason,
            'danger',
            'ri-close-circle-line'
        ));

        return $this->success($message, 'Message rejected successfully', 200);
    }

    private function validatePayload(Request $request): void
    {
        $request->validate([
            'client_message_type_id' => ['required', 'exists:client_message_types,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'profile_name' => ['required', 'string', 'max:255'],
            'last_message_type' => ['required', Rule::in(['none', 'image', 'multiple'])],
            'last_message_files' => ['nullable', 'array'],
            'last_message_files.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'their_message' => ['required', 'string'],
            'attachment_files' => ['nullable', 'array'],
            'attachment_files.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $lastMessageFiles = $request->file('last_message_files', []);
        if ($request->input('last_message_type') === 'image' && count($lastMessageFiles) > 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'last_message_files' => 'Only one screenshot is allowed when "Image" is selected. Choose "Multiple" to upload more than one.',
            ]);
        }
    }

    private function storeAttachments(ClientMessage $message, array $files, string $type): void
    {
        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $directory = "client-messages/{$message->team_id}/{$message->id}";
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $file->move(public_path($directory), $filename);

            ClientMessageAttachment::create([
                'client_message_id' => $message->id,
                'type' => $type,
                'original_name' => $originalName,
                'path' => $directory . '/' . $filename,
            ]);
        }
    }

    private function deleteAttachments(ClientMessage $message, string $type): void
    {
        $attachments = $message->attachments()->where('type', $type)->get();

        foreach ($attachments as $attachment) {
            $fullPath = public_path($attachment->path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $attachment->delete();
        }
    }

    private function notifyReviewers(Team $team, User $submitter, ClientMessage $message, ClientMessageType $type): void
    {
        $recipients = User::where('team_id', $team->id)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Leader', 'Co Leader']))
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new AdminNotification(
            'New Client Message Awaiting Approval',
            $submitter->name . ' submitted a "' . $type->name . '" message for client ' . $message->client_name . '.',
            'info',
            'ri-mail-send-line'
        ));
    }
}
