<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Requests\Chats\ChatListRequest;
use App\Http\Requests\Chats\ChatStoreRequest;
use App\Http\Requests\Chats\SendMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    use Responses;

    public function index(ChatListRequest $request)
    {
        return User::find($request->get('userId'))->chats()->paginate(20);
    }

    public function store(ChatStoreRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $chat = new Chat();
        $chat->users()->associate($user);
        $chat->users()->associate($data['partner_id']);
        $chat->save();

        return $this->successResponseWithData($chat->with('users'));
    }

    public function show(Chat $chat): JsonResponse
    {
        $chat = Chat::findOrFail($chat->id);

        if (!in_array(auth()->id(), $chat->users()->pluck('id')->toArray())) {
            $this->exceptionResponse(
                new \Exception('Access denied'),
                403
            );
        }

        return $this->successResponseWithData($chat->with('users'));
    }

    public function destroy(Chat $chat): JsonResponse
    {
        $chat->delete();
        return $this->successResponse();
    }

    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $message = Message::create([
            'message' => $data['message'],
            'chat_id' => $data['chat_id'],
            'user_id' => auth()->id(),
        ]);

        return $this->successResponseWithData(
            $message->chat()->with(['users', 'messages'])->first(),
        );
    }
}
