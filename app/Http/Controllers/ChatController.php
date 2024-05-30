<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Requests\Chats\ChatStoreRequest;
use App\Http\Requests\Chats\SendMessageRequest;
use App\Models\Chat;
use App\Models\Message;
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
        SA\Response(content: '
         "data": [
        {
            "id": 1,
            "owner_id": 1,
            "partner_id": 2,
            "messages": [
                {
                    "id": 1,
                    "user_id": 1,
                    "chat_id": 1,
                    "message": "fuga",
                    "created_at": "2024-05-29T11:55:16.000000Z"
                }
            ]
        }
    ]', status: Response::HTTP_OK, description: 'OK'),
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
        SA\Response(content: '
        {
    "data": [
        {
            "id": 1,
            "owner_id": 1,
            "partner_id": 2,
            "owner": {
                "id": 1,
                "first_name": "Sergey",
                "last_name": "Pupkin",
                "email": "spam@runum.ru"
            },
            "partner": {
                "id": 2,
                "first_name": "Mark",
                "last_name": "Pupkin",
                "email": "spam2@runum.ru"
            }
        }
    ]
}
        ', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function store(ChatStoreRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $chat = Chat::firstOrCreate([
            'owner_id' => $user->id,
            'partner_id' => $data['partner_id'],
        ]);

        return $this->successResponseWithData($chat->with(['owner', 'partner'])->first());
    }

    #[
        SA\Endpoint(
            title: 'Показать чат',
            description: 'Возвращает список чатов авторизированного пользователя'
        ),
        SA\Response(content: '
        {
    "data": [
        [
            {
                "id": 1,
                "owner_id": 1,
                "partner_id": 2,
                "owner": {
                    "id": 1,
                    "first_name": "Sergey",
                    "last_name": "Pupkin",
                    "email": "spam@runum.ru"
                },
                "partner": {
                    "id": 2,
                    "first_name": "Mark",
                    "last_name": "Pupkin",
                    "email": "spam2@runum.ru"
                },
                "messages": [
                    {
                        "id": 1,
                        "user_id": 1,
                        "chat_id": 1,
                        "message": "fuga",
                        "created_at": "2024-05-29T11:55:16.000000Z"
                    }
                ]
            }
        ]
    ]
}
        ', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function show(int $chatId): JsonResponse
    {
        $chat = Chat::find($chatId);

        if ($chat === null) {
            $this->errorResponse(
                'Chat not found',
                404
            );
        }

        $this->checkAccess($chat);

        return $this->successResponseWithData($chat->with(['owner', 'partner', 'messages']));
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
        SA\Response(content: '
        {
    "data": [
        {
            "id": 1,
            "owner_id": 1,
            "partner_id": 2,
            "owner": {
                "id": 1,
                "first_name": "Sergey",
                "last_name": "Pupkin",
                "email": "spam@runum.ru"
            },
            "partner": {
                "id": 2,
                "first_name": "Mark",
                "last_name": "Pupkin",
                "email": "spam2@runum.ru"
            },
            "messages": [
                {
                    "id": 1,
                    "user_id": 1,
                    "chat_id": 1,
                    "message": "fuga",
                    "created_at": "2024-05-29T11:55:16.000000Z"
                },
                {
                    "id": 2,
                    "user_id": 1,
                    "chat_id": 1,
                    "message": "fuga",
                    "created_at": "2024-05-29T14:20:56.000000Z"
                }
            ]
        }
    ]
}
        ', status: Response::HTTP_OK, description: 'OK'),
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
