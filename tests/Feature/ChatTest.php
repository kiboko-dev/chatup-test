<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_chat_store()
    {
        $response = $this
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('/api/v1/auth/register', [
                'partner_id' => 2,
            ]);

        $response->assertStatus(302);
    }

    public function test_chat_show()
    {
        $chat = $this->getChat();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ])->withToken('1|QHVu1gHcqfAqxEsu5VHCxSc4FCv5T476hccYTUA5dbd8b07e')
            ->get('api/v1/chat/'.$chat->id);

        $response->assertStatus(200);
    }

    public function test_chats_list()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ])->withToken('1|QHVu1gHcqfAqxEsu5VHCxSc4FCv5T476hccYTUA5dbd8b07e')
            ->get('api/v1/chats');

        $response->assertStatus(200);
    }

    public function test_message_send()
    {
        $partner = User::create([
            'email' => "test".rand(1, 100)."@test.com",
            'password' => 'pass13324',
            'last_name' => 'Testov',
            'first_name' => 'Test',
        ]);
        $owner = User::create([
            'email' => "test".rand(1, 100)."@test.com",
            'password' => 'pass13324',
            'last_name' => 'Testov',
            'first_name' => 'Test',
        ]);
        $chat = $this->getChat(ownerId: $owner->id, partnerId: $partner->id);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ])->withToken('1|QHVu1gHcqfAqxEsu5VHCxSc4FCv5T476hccYTUA5dbd8b07e')
            ->post('api/v1/chat/send-message', [
                'chat_id' => $chat->id,
                'message' => 'test message'
            ]);

        $response->assertStatus(422);
    }

    public function test_chat_destroy()
    {
        $chat = $this->getChat();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ])->withToken('1|QHVu1gHcqfAqxEsu5VHCxSc4FCv5T476hccYTUA5dbd8b07e')
            ->delete('api/v1/chat/'.$chat->id);

        $response->assertStatus(200);
    }

    private function getChat(int $ownerId = 1, int $partnerId = 2): Chat
    {
        $chat = Chat::first();
        if (null === $chat) {
            $chat = Chat::create([
                'owner_id' => $ownerId,
                'partner_id' => $partnerId,
            ]);
        }

        return $chat;
    }
}
