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
use Knuckles\Scribe\Attributes as SA;
use Symfony\Component\HttpFoundation\Response;

#[
    SA\Group('V1'),
    SA\Subgroup('Чаты и сообщения')
]
class ChatController extends Controller
{
    use Responses;

    #[
        SA\Endpoint(
            title: 'Список чатов',
            description: 'Возвращает список чатов авторизированного пользователя'
        ),
        SA\Response(content: '', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function index()
    {
        return Chat::where('owner_id', auth()->id())
            ->orWhere('partner_id', auth()->id())
            ->with('messages')
            ->paginate(20);

    }

    #[
        SA\Endpoint(
            title: 'Создать чат',
            description: 'Возвращает список чатов авторизированного пользователя'
        ),
        SA\Response(content: '', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function store(ChatStoreRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $chat = Chat::create([
            'owner_id' => $user->id,
            'partner_id' => $data['partner_id'],
        ]);

        return $this->successResponseWithData($chat->with(['owner', 'partner'])->get());
    }

    #[
        SA\Endpoint(
            title: 'Показать чат',
            description: 'Возвращает список чатов авторизированного пользователя'
        ),
        SA\Response(content: '', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function show(int $chatId): JsonResponse
    {
        $chat = Chat::find($chatId);

        if (null === $chat) {
            $this->errorResponse(
                'Chat not found',
                404
            );
        }

        $this->checkAccess($chat);

        return $this->successResponseWithData($chat->with(['owner', 'partner', 'messages'])->get());
    }

    #[
        SA\Endpoint(
            title: 'Удалить чат',
            description: 'Возвращает список чатов авторизированного пользователя'
        ),
        SA\Response(content: '', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function destroy(Chat $chat): JsonResponse
    {
        $this->checkAccess($chat);
        $chat->delete();
        return $this->successResponse();
    }

    #[
        SA\Endpoint(
            title: 'Отправить сообщение в чат',
            description: 'Возвращает список чатов авторизированного пользователя'
        ),
        SA\Response(content: '', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->checkAccess(Chat::find($data['chat_id']));

        $message = Message::create([
            'message' => $data['message'],
            'chat_id' => $data['chat_id'],
            'user_id' => auth()->id(),
        ]);

        return $this->successResponseWithData(
            $message->chat()->with(['owner', 'partner', 'messages'])->first(),
        );
    }

    private function checkAccess(Chat $chat): void
    {
        if (auth()->id() !== $chat->owner_id && auth()->id() !== $chat->partner_id) {
            $this->errorResponse(
                'Access denied',
                403
            );
        }
    }
}
