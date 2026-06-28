<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_can_use_gemini_for_general_questions(): void
    {
        Config::set('services.gemini.key', 'test-gemini-key');
        Config::set('services.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'NAT giúp nhiều thiết bị trong mạng nội bộ dùng chung một địa chỉ IP public.'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $this->postJson(route('chatbot.reply'), [
            'message' => 'NAT là gì?',
        ])
            ->assertOk()
            ->assertJsonPath('reply', 'NAT giúp nhiều thiết bị trong mạng nội bộ dùng chung một địa chỉ IP public.');

        Http::assertSent(fn (Request $request): bool => str_contains($request->body(), 'NAT'));
    }

    public function test_chatbot_recommends_products_from_local_catalog(): void
    {
        Product::factory()->create([
            'name' => 'Router WiFi 6 AX1800',
            'sku' => 'HLA-ROUTER-TEST',
            'category' => Product::CATEGORY_ROUTER,
            'price' => 1200000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->postJson(route('chatbot.reply'), [
            'message' => 'Có mẫu router nào phù hợp không?',
        ])
            ->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Router WiFi 6 AX1800'));
    }

    public function test_chatbot_recommends_cheap_wifi_products_from_local_catalog(): void
    {
        Product::factory()->create([
            'name' => 'USB WiFi Mini AC600',
            'sku' => 'HLA-USB-CHEAP',
            'category' => Product::CATEGORY_USB_WIFI,
            'price' => 350000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->postJson(route('chatbot.reply'), [
            'message' => 'Gợi ý thiết bị WiFi giá rẻ',
        ])
            ->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'USB WiFi Mini AC600'));
    }

    public function test_chatbot_uses_local_reply_when_gemini_api_key_is_missing(): void
    {
        Config::set('services.gemini.key', null);

        $this->postJson(route('chatbot.reply'), [
            'message' => 'Xin chào',
        ])
            ->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'HLA Wifi Shop'));
    }
}
