<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Events\ChatMessageSent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\AjaxResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use AjaxResponse;
    public function sendMessage(Request $request)
    {
        $receiverId = $request->receiver_id;
        // Generate a unique conversation id based on the two user ids
        $conversationId = implode('-', [min(Auth::user()->id, $receiverId), max(Auth::user()->id, $receiverId)]);

        $chat = Chat::create([
            'sender_id'       => Auth::user()->id,
            'receiver_id'     => $receiverId,
            'message'         => $request->message,
            'conversation_id' => $conversationId,
        ]);

        // Broadcast the message
        broadcast(new ChatMessageSent($chat))->toOthers();

        return $this->success($chat, 'Message sent successfully', 200);
    }

    public function fetchMessages(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id'
        ]);

        $receiverId = $request->receiver_id;
        $authUserId = Auth::id();

        // Generate conversation ID
        $conversationId = implode('-', [min($authUserId, $receiverId), max($authUserId, $receiverId)]);

        Chat::where('conversation_id', $conversationId)
            ->where('receiver_id', $authUserId)
            ->where('read', false)
            ->update(['read' => true]);

        // Get messages
        $data['messages'] = Chat::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->with(['sender', 'receiver'])
            ->get();

        // Get user info
        $data['receiver'] = User::find($receiverId);

        return $this->success($data, 'Data Fetch Success', 200);
    }

    public function destroyMessages(Request $request)
    {
        Chat::where('id', $request->message_id)->delete();

        return $this->success([], 'Messages deleted successfully', 200);
    }

    public function getUsers(Request $request)
    {
        $authUserId = Auth::id();

        $data['contact_users'] = User::whereHas('sentChats', function ($query) use ($authUserId) {
            $query->where('receiver_id', $authUserId);
        })
            ->orWhereHas('receivedChats', function ($query) use ($authUserId) {
                $query->where('sender_id', $authUserId);
            })
            ->with(['sentChats', 'receivedChats'])
            ->get()
            ->map(function ($user) use ($authUserId) {
                // Get the last message for each user based on conversation
                $lastMessage = Chat::where(function ($query) use ($user, $authUserId) {
                    $query->where('sender_id', $authUserId)
                        ->where('receiver_id', $user->id);
                })
                    ->orWhere(function ($query) use ($user, $authUserId) {
                        $query->where('sender_id', $user->id)
                            ->where('receiver_id', $authUserId);
                    })
                    ->latest() // Get the most recent message
                    ->first();

                // Add last message and time ago
                $user->last_message = $lastMessage ? $lastMessage->message : null;
                $user->last_message_time = $lastMessage ? $this->timeAgo($lastMessage->created_at) : null;

                // Store last message timestamp for sorting
                $user->last_message_timestamp = $lastMessage ? $lastMessage->created_at : null;

                // Add unread count
                $user->unread_count = $user->sentChats()->where('read', false)->count();

                return $user;
            })
            ->sortByDesc('last_message_timestamp') // Sort users by last message timestamp in descending order
            ->values();

        $data['users'] = User::where('id', '!=', $authUserId)->whereDoesntHave('sentChats', function ($query) use ($authUserId) {
            $query->where('receiver_id', $authUserId);
        })
            ->whereDoesntHave('receivedChats', function ($query) use ($authUserId) {
                $query->where('sender_id', $authUserId);
            })
            ->get();


        return $this->success($data, 'Data Fetch Success', 200);
    }

    // Helper function to calculate time ago
    private function timeAgo($date)
    {
        $diff = Carbon::parse($date)->diffInMinutes(now());

        // Round down to the nearest integer for minutes
        if ($diff < 60) {
            return intval($diff) . " min ago";
        } elseif ($diff < 1440) { // 24 hours
            $hours = floor($diff / 60);
            return intval($hours) . " hr ago";
        } elseif ($diff < 43200) { // 30 days
            $days = floor($diff / 1440);
            return intval($days) . " days ago";
        } else {
            $months = floor($diff / 43200);
            return intval($months) . " months ago";
        }
    }
}
