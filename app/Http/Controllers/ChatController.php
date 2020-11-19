<?php


namespace App\Http\Controllers;


use App\Events\MessageReceived;
use App\Events\UserNotificationReceived;
use App\Helpers\UserNotification;
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

    public function chatWithUser($userId = null)
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

        $distinctSenderReceiver = ChatMessage::distinct()->select('sender_id', 'receiver_id')->where('sender_id', '=', Auth::id())
            ->orWhere('receiver_id', '=', Auth::id())->get();

        $distinctContactIds = [];
        foreach ($distinctSenderReceiver as $senderReceiver) {
            if (!array_key_exists($senderReceiver->sender_id, $distinctContactIds) && !array_key_exists($senderReceiver->receiver_id, $distinctContactIds)) {
                $distinctContactIds[] = $senderReceiver->sender_id === Auth::id() ? $senderReceiver->receiver_id : $senderReceiver->sender_id;
            }
        }

        $distinctContacts = User::findMany($distinctContactIds);

        $distinctContacts = $distinctContacts->filter(function ($contact) use ($userId) {
            return $contact->id !== (int) $userId;
        });

        return Inertia::render('Chat', [
            'currentUser' => Auth::user(),
            'receiver' => User::find($userId),
            'chatMessages' => $chatMessages,
            'contacts' => $distinctContacts
        ]);
    }

    public function sendChatMessage(Request $request, $userId)
    {
        $chatMessage = new ChatMessage();
        $chatMessage->sender_id = Auth::user()->id;
        $chatMessage->receiver_id = $userId;
        $chatMessage->chat_message = $request->input('chatMessageInput');

        $chatMessage->save();

        $userNotificationMessage = 'New Chat Message from ' . User::find(Auth::id())->name;

        $userNotification = new UserNotification();
        $userNotification->setUserId($chatMessage->receiver_id)
            ->setType('chatMessage')
            ->setMessage($userNotificationMessage)
            ->setSenderId(Auth::id());

        event(new UserNotificationReceived($userNotification));
//        event(new MessageReceived($chatMessage));

        return Redirect::route('chat', [
            'userId' => $userId
        ]);
    }

}
