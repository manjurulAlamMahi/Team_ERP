<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    use AjaxResponse;

    public function sendMessage(Request $request)
    {
        $receiverId = $request->receiver_id;
        // Generate a unique conversation id based on the two user ids
        $conversationId = implode('-', [min(Auth::user()->id, $receiverId), max(Auth::user()->id, $receiverId)]);

        $chat = Chat ::create([
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
        $receiverId = $request->request_id;
        $conversationId = implode('-', [min(Auth::user()->id, $receiverId), max(Auth::user()->id, $receiverId)]);

        $messages = Chat::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->success($messages, 'Data Fetch Success', 200);
    }
}
