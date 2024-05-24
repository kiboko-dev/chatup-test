<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Requests\ChatListRequest;
use App\Models\Chat;
use App\Models\User;

class ChatController extends Controller
{
    use Responses;

    public function index(ChatListRequest $request)
    {
        $chats = Chat::all()->paginate();

        if ($request->has('userId')) {
            $chats = User::find($request->get('userId'))->chats();
        }

        return $this->successResponseWithData($chats);
    }

    public function store()
    {
    }

    public function show()
    {
    }

    public function destroy()
    {
    }
}
