<?php


namespace App\Http\Controllers;


use App\Events\MessageReceived;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function chatWithUser($userId)
    {
        if ((int)$userId === Auth::user()->getAuthIdentifier()) {
            return Redirect::route('lonely-dashboard');
        }

        $chatMessages = ChatMessage::where([
            ['sender_id', '=', Auth::user()->id],
            ['receiver_id', '=', $userId]
        ])
            ->orWhere([
                ['receiver_id', '=', Auth::user()->id],
                ['sender_id', '=', $userId]
            ])
            ->get();

        return Inertia::render('Chat', [
            'currentUser' => Auth::user(),
            'receiver' => User::find($userId),
            'chatMessages' => $chatMessages
        ]);
    }

    public function sendChatMessage(Request $request, $userId)
    {
        $chatMessage = new ChatMessage();
        $chatMessage->sender_id = Auth::user()->id;
        $chatMessage->receiver_id = $userId;
        $chatMessage->chat_message = $request->input('chatMessageInput');

        $chatMessage->save();

        event(new MessageReceived($chatMessage));

        return Redirect::route('chat', [
            'userId' => $userId
        ]);
    }

}
