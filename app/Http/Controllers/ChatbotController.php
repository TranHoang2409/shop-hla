<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ChatbotController extends Controller
{
    public function reply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = trim($validated['message']);
        $localReply = $this->localReply($message, $request);

        if ($this->shouldUseLocalReply($message)) {
            return response()->json([
                'reply' => $localReply['reply'],
                'suggestions' => $localReply['suggestions'],
            ]);
        }

        try {
            $reply = $this->askGemini($message, $request, $localReply['reply']);
        } catch (Throwable $exception) {
            Log::info('Chatbot is using local fallback.', [
                'reason' => $exception->getMessage(),
            ]);

            $reply = $localReply['reply'];
        }

        return response()->json([
            'reply' => $reply,
            'suggestions' => $localReply['suggestions'],
        ]);
    }

    private function askGemini(string $message, Request $request, string $localReply): string
    {
        $apiKey = trim((string) config('services.gemini.key'));

        if ($apiKey === '') {
            throw new \RuntimeException('Gemini API key is not configured.');
        }

        $model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            rawurlencode($model)
        );

        $response = Http::timeout((int) config('services.gemini.timeout', 20))
            ->acceptJson()
            ->post($endpoint . '?key=' . urlencode($apiKey), [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $this->buildPrompt($message, $request, $localReply)],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.35,
                    'maxOutputTokens' => 500,
                ],
            ]);

        if ($response->failed()) {
            Log::warning('Gemini chatbot request failed.', [
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 1000),
            ]);

            throw new \RuntimeException('Gemini request failed.');
        }

        $reply = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! is_string($reply) || trim($reply) === '') {
            throw new \RuntimeException('Gemini returned an empty reply.');
        }

        return Str::of($reply)
            ->stripTags()
            ->replaceMatches("/\n{3,}/", "\n\n")
            ->trim()
            ->toString();
    }

    private function buildPrompt(string $message, Request $request, string $localReply): string
    {
        return implode("\n\n", [
            'Bạn là trợ lý bán hàng cho website HLA Wifi Shop. Nhiệm vụ: tư vấn thiết bị WiFi, hướng dẫn mua hàng, bảo hành, giao hàng, thanh toán, giỏ hàng và đơn hàng.',
            'Luôn trả lời bằng tiếng Việt, thân thiện, ngắn gọn. Không bịa tồn kho, giá, đơn hàng hoặc thông tin khách. Nếu dữ liệu chưa có, hãy nói rõ và hướng dẫn khách vào trang phù hợp.',
            'Thông tin cố định: hotline 0900 000 001, email support@hla.test. Shop hỗ trợ COD và chuyển khoản ngân hàng. Đơn từ 500.000 VNĐ được miễn phí vận chuyển. Sản phẩm bảo hành theo từng thiết bị, khách cần giữ hóa đơn hoặc thông tin đơn hàng.',
            'Dữ liệu khách hiện tại:',
            $this->customerContext($request),
            'Sản phẩm liên quan trong hệ thống:',
            $this->productContext($message),
            'Câu trả lời nội bộ đề xuất, hãy dùng làm nguồn sự thật:',
            $localReply,
            'Câu hỏi của khách: ' . $message,
        ]);
    }

    private function localReply(string $message, Request $request): array
    {
        $normalized = $this->normalize($message);

        if ($this->containsAny($normalized, ['gio hang', 'cart', 'da chon', 'san pham trong gio'])) {
            return $this->cartReply($request);
        }

        if ($this->containsAny($normalized, ['don hang', 'trang thai don', 'kiem tra don', 'lich su mua'])) {
            return $this->orderReply($request);
        }

        if ($this->containsAny($normalized, ['bao hanh', 'doi tra', 'loi san pham'])) {
            return [
                'reply' => 'Sản phẩm tại HLA Wifi Shop được bảo hành theo từng thiết bị. Khi cần hỗ trợ, bạn giữ hóa đơn hoặc thông tin đơn hàng và liên hệ hotline 0900 000 001. Nếu sản phẩm lỗi, shop sẽ kiểm tra tình trạng và hướng dẫn bảo hành/đổi trả theo chính sách.',
                'suggestions' => $this->supportSuggestions(),
            ];
        }

        if ($this->containsAny($normalized, ['giao hang', 'van chuyen', 'ship', 'nhan hang'])) {
            return [
                'reply' => 'Shop hỗ trợ giao hàng cho đơn mua thiết bị WiFi. Đơn từ 500.000 VNĐ được miễn phí vận chuyển. Sau khi đặt hàng, bạn có thể theo dõi trạng thái trong mục Đơn hàng nếu đã đăng nhập.',
                'suggestions' => $this->buyingSuggestions(),
            ];
        }

        if ($this->containsAny($normalized, ['thanh toan', 'cod', 'chuyen khoan', 'ngan hang'])) {
            return [
                'reply' => 'HLA Wifi Shop hỗ trợ thanh toán COD và chuyển khoản ngân hàng. Nếu chọn chuyển khoản, thông tin tài khoản sẽ hiển thị ở bước đặt hàng và trong chi tiết đơn để bạn đối chiếu.',
                'suggestions' => $this->buyingSuggestions(),
            ];
        }

        if ($this->containsAny($normalized, ['lien he', 'hotline', 'email', 'tu van truc tiep'])) {
            return [
                'reply' => 'Bạn có thể liên hệ HLA Wifi Shop qua hotline 0900 000 001 hoặc email support@hla.test. Bạn cũng có thể mô tả diện tích nhà, số tầng và số thiết bị để mình tư vấn mẫu phù hợp ngay tại đây.',
                'suggestions' => $this->productSuggestions(),
            ];
        }

        if ($this->looksLikeProductQuestion($normalized)) {
            return $this->productReply($message);
        }

        if ($this->containsAny($normalized, ['xin chao', 'chao', 'hello', 'hi'])) {
            return [
                'reply' => 'Chào bạn! Mình có thể tư vấn router, mesh WiFi, thiết bị mạng, kiểm tra giỏ hàng, hướng dẫn đặt hàng, thanh toán, giao hàng và bảo hành cho HLA Wifi Shop.',
                'suggestions' => $this->defaultSuggestions(),
            ];
        }

        return [
            'reply' => 'Mình có thể hỗ trợ bạn về sản phẩm WiFi, chọn router/mesh, giỏ hàng, đặt hàng, thanh toán, giao hàng và bảo hành. Bạn cho mình biết nhu cầu, ví dụ diện tích nhà, số tầng, số người dùng hoặc ngân sách nhé.',
            'suggestions' => $this->defaultSuggestions(),
        ];
    }

    private function shouldUseLocalReply(string $message): bool
    {
        $normalized = $this->normalize($message);

        return $this->looksLikeProductQuestion($normalized)
            || $this->containsAny($normalized, [
                'gio hang',
                'cart',
                'da chon',
                'san pham trong gio',
                'don hang',
                'trang thai don',
                'kiem tra don',
                'lich su mua',
                'bao hanh',
                'doi tra',
                'loi san pham',
                'giao hang',
                'van chuyen',
                'ship',
                'nhan hang',
                'thanh toan',
                'cod',
                'chuyen khoan',
                'ngan hang',
                'lien he',
                'hotline',
                'email',
                'tu van truc tiep',
                'xin chao',
                'chao',
                'hello',
                'hi',
            ]);
    }

    private function productReply(string $message): array
    {
        $products = $this->matchingProducts($message, 3);

        if ($products->isEmpty()) {
            return [
                'reply' => 'Mình chưa tìm thấy sản phẩm thật khớp với nhu cầu này. Bạn có thể thử hỏi theo nhóm như router, mesh WiFi, access point, switch, USB WiFi, camera hoặc bộ mở rộng sóng.',
                'suggestions' => $this->productSuggestions(),
            ];
        }

        $lines = $products
            ->map(fn (Product $product): string => sprintf(
                '- %s: %s VNĐ, còn %s sản phẩm, danh mục %s.',
                $product->name,
                number_format((float) $product->price),
                number_format((int) $product->stock),
                $product->categoryName()
            ))
            ->implode("\n");

        return [
            'reply' => "Mình gợi ý các sản phẩm đang có trong shop:\n{$lines}\nBạn có thể vào trang Sản phẩm để xem chi tiết và thêm vào giỏ hàng.",
            'suggestions' => $this->buyingSuggestions(),
        ];
    }

    private function cartReply(Request $request): array
    {
        $cart = collect($request->session()->get('cart', []));

        if ($cart->isEmpty()) {
            return [
                'reply' => 'Giỏ hàng của bạn hiện đang trống. Bạn có thể hỏi mình tư vấn router hoặc mesh WiFi theo diện tích nhà, số tầng và ngân sách để chọn sản phẩm trước.',
                'suggestions' => $this->productSuggestions(),
            ];
        }

        $total = $cart->sum(fn (array $item): float => ((float) $item['price']) * ((int) $item['quantity']));
        $items = $cart
            ->take(4)
            ->map(fn (array $item): string => sprintf(
                '- %s x%s: %s VNĐ',
                $item['name'],
                number_format((int) $item['quantity']),
                number_format(((float) $item['price']) * ((int) $item['quantity']))
            ))
            ->implode("\n");

        return [
            'reply' => "Giỏ hàng của bạn đang có:\n{$items}\nTạm tính: " . number_format($total) . " VNĐ. Bạn có thể vào Giỏ hàng để kiểm tra số lượng rồi tiến hành đặt hàng.",
            'suggestions' => [
                ['label' => 'Thanh toán', 'message' => 'Shop hỗ trợ thanh toán nào?'],
                ['label' => 'Giao hàng', 'message' => 'Shop giao hàng thế nào?'],
                ['label' => 'Tư vấn thêm', 'message' => 'Gợi ý thêm sản phẩm WiFi cho tôi'],
            ],
        ];
    }

    private function orderReply(Request $request): array
    {
        if (! $request->user()) {
            return [
                'reply' => 'Bạn cần đăng nhập tài khoản khách hàng để xem lịch sử và trạng thái đơn hàng. Sau khi đăng nhập, vào mục Đơn hàng để kiểm tra đơn mới nhất.',
                'suggestions' => $this->buyingSuggestions(),
            ];
        }

        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest('placed_at')
            ->take(3)
            ->get();

        if ($orders->isEmpty()) {
            return [
                'reply' => 'Tài khoản của bạn chưa có đơn hàng nào. Bạn có thể chọn sản phẩm ở trang Sản phẩm rồi thêm vào giỏ để đặt hàng.',
                'suggestions' => $this->productSuggestions(),
            ];
        }

        $lines = $orders
            ->map(fn (Order $order): string => sprintf(
                '- %s: %s, thanh toán %s, tổng %s VNĐ.',
                $order->order_number,
                $order->statusLabel(),
                $order->paymentStatusLabel(),
                number_format((float) $order->total)
            ))
            ->implode("\n");

        return [
            'reply' => "Các đơn gần đây của bạn:\n{$lines}\nBạn có thể vào mục Đơn hàng để xem chi tiết từng đơn.",
            'suggestions' => $this->buyingSuggestions(),
        ];
    }

    private function productContext(string $message): string
    {
        $products = $this->matchingProducts($message, 8);

        if ($products->isEmpty()) {
            $products = Product::query()
                ->active()
                ->orderByDesc('stock')
                ->orderBy('price')
                ->take(8)
                ->get(['id', 'name', 'sku', 'category', 'price', 'stock', 'description']);
        }

        if ($products->isEmpty()) {
            return '- Chưa có dữ liệu sản phẩm đang bán trong hệ thống.';
        }

        return $products
            ->map(function (Product $product): string {
                return sprintf(
                    '- %s (%s), danh mục %s, giá %s VNĐ, còn %s sản phẩm. %s',
                    $product->name,
                    $product->sku ?: 'chưa có SKU',
                    $product->categoryName(),
                    number_format((float) $product->price),
                    number_format((int) $product->stock),
                    Str::limit((string) $product->description, 140)
                );
            })
            ->implode("\n");
    }

    private function matchingProducts(string $message, int $limit): Collection
    {
        $normalized = $this->normalize($message);
        $query = Product::query()->active();
        $category = $this->categoryFromMessage($normalized);

        if ($category) {
            $query->where('category', $category);
        } elseif ($this->containsAny($normalized, ['wifi', 'thiet bi', 'san pham', 'goi y', 'tu van', 'mua', 'gia re'])) {
            // Generic shopping questions should browse active products instead of searching the whole sentence.
        } else {
            $query->search($message);
        }

        if ($this->containsAny($normalized, ['gia re', 're nhat', 'duoi 1 trieu', 'duoi mot trieu'])) {
            $query->where('price', '<=', 1000000)->orderBy('price');
        } else {
            $query->orderByDesc('stock')->orderBy('price');
        }

        return $query
            ->take($limit)
            ->get(['id', 'name', 'sku', 'category', 'price', 'stock', 'description']);
    }

    private function customerContext(Request $request): string
    {
        $cartCount = collect($request->session()->get('cart', []))
            ->sum(fn (array $item): int => (int) $item['quantity']);

        if (! $request->user()) {
            return "- Khách chưa đăng nhập.\n- Giỏ hàng hiện có {$cartCount} sản phẩm.";
        }

        $latestOrder = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest('placed_at')
            ->first();

        $orderLine = $latestOrder
            ? "- Đơn gần nhất {$latestOrder->order_number}: {$latestOrder->statusLabel()}, thanh toán {$latestOrder->paymentStatusLabel()}."
            : '- Khách chưa có đơn hàng.';

        return implode("\n", [
            '- Khách đã đăng nhập: ' . $request->user()->name,
            "- Giỏ hàng hiện có {$cartCount} sản phẩm.",
            $orderLine,
        ]);
    }

    private function categoryFromMessage(string $message): ?string
    {
        return match (true) {
            str_contains($message, 'mesh') => Product::CATEGORY_MESH_WIFI,
            str_contains($message, 'router') || str_contains($message, 'bo phat') => Product::CATEGORY_ROUTER,
            str_contains($message, 'access point') || str_contains($message, 'ap ') => Product::CATEGORY_ACCESS_POINT,
            str_contains($message, 'switch') => Product::CATEGORY_SWITCH,
            str_contains($message, 'usb') => Product::CATEGORY_USB_WIFI,
            str_contains($message, 'camera') || str_contains($message, 'cam') => Product::CATEGORY_CAMERA,
            str_contains($message, 'mo rong') || str_contains($message, 'extender') || str_contains($message, 'repeater') => Product::CATEGORY_EXTENDER,
            default => null,
        };
    }

    private function looksLikeProductQuestion(string $message): bool
    {
        return $this->containsAny($message, [
            'tu van',
            'goi y',
            'san pham',
            'wifi',
            'router',
            'mesh',
            'access point',
            'camera',
            'switch',
            'usb',
            'mo rong',
            'extender',
            'repeater',
            'gia re',
            'phu song',
            'mua',
        ]);
    }

    private function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $message): string
    {
        return Str::of($message)
            ->lower()
            ->ascii()
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }

    private function defaultSuggestions(): array
    {
        return [
            ['label' => 'Tư vấn router', 'message' => 'Tư vấn router cho gia đình'],
            ['label' => 'Mesh WiFi', 'message' => 'Tư vấn mesh WiFi phủ sóng toàn nhà'],
            ['label' => 'Kiểm tra giỏ', 'message' => 'Giỏ hàng của tôi có gì?'],
        ];
    }

    private function productSuggestions(): array
    {
        return [
            ['label' => 'Router gia đình', 'message' => 'Tư vấn router cho gia đình'],
            ['label' => 'Mesh WiFi', 'message' => 'Tư vấn mesh WiFi phủ sóng toàn nhà'],
            ['label' => 'Giá rẻ', 'message' => 'Gợi ý thiết bị WiFi giá rẻ'],
        ];
    }

    private function buyingSuggestions(): array
    {
        return [
            ['label' => 'Giỏ hàng', 'message' => 'Giỏ hàng của tôi có gì?'],
            ['label' => 'Thanh toán', 'message' => 'Shop hỗ trợ thanh toán nào?'],
            ['label' => 'Giao hàng', 'message' => 'Shop giao hàng thế nào?'],
        ];
    }

    private function supportSuggestions(): array
    {
        return [
            ['label' => 'Liên hệ shop', 'message' => 'Tôi muốn liên hệ hỗ trợ'],
            ['label' => 'Đơn hàng', 'message' => 'Tôi muốn kiểm tra đơn hàng'],
            ['label' => 'Tư vấn thêm', 'message' => 'Gợi ý sản phẩm WiFi cho tôi'],
        ];
    }
}
