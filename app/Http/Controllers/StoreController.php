<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function home()
    {
        $categoryCounts = Product::active()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('frontend.home', [
            'featuredProducts' => Product::active()->latest()->take(6)->get(),
            'productCount' => Product::active()->count(),
            'categories' => Product::CATEGORIES,
            'categoryCards' => collect(Product::categoryOptions())
                ->map(function (array $category, string $key) use ($categoryCounts): array {
                    return [
                        ...$category,
                        'key' => $key,
                        'total' => (int) ($categoryCounts[$key] ?? 0),
                    ];
                })
                ->values(),
        ]);
    }

    public function shop(Request $request)
    {
        $query = Product::query()->active();
        $category = $request->string('category')->toString();
        $minPrice = $request->integer('min_price');
        $maxPrice = $request->integer('max_price');

        $query->search($request->string('q')->toString());
        $query->inCategory($category);

        if ($minPrice > 0) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice > 0 && $maxPrice >= $minPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        match ($request->string('stock')->toString()) {
            'in_stock' => $query->where('stock', '>', 0),
            'out_of_stock' => $query->where('stock', '<=', 0),
            default => null,
        };

        $categoryCounts = Product::active()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $selectedCategory = Product::isValidCategory($category)
            ? [
                ...Product::categoryOptions()[$category],
                'key' => $category,
                'total' => (int) ($categoryCounts[$category] ?? 0),
            ]
            : null;

        if (! Product::isValidCategory($category)) {
            $category = '';
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            'stock_desc' => $query->orderByDesc('stock'),
            default => $query->latest(),
        };

        return view('frontend.catalog.index', [
            'products' => $query->paginate(9)->withQueryString(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'category' => $category,
                'min_price' => $request->string('min_price')->toString(),
                'max_price' => $request->string('max_price')->toString(),
                'stock' => $request->string('stock')->toString(),
                'sort' => $request->string('sort')->toString(),
            ],
            'categories' => Product::CATEGORIES,
            'categoryOptions' => Product::categoryOptions(),
            'categoryCounts' => $categoryCounts,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('frontend.catalog.show', [
            'product' => $product,
            'relatedProducts' => Product::active()
                ->where('category', $product->category)
                ->whereKeyNot($product->id)
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}
