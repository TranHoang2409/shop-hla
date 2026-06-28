<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->default(Product::CATEGORY_ROUTER)->after('name');
        });

        DB::table('products')->orderBy('id')->get()->each(function (object $product): void {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['category' => $this->guessCategory($product)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    private function guessCategory(object $product): string
    {
        $text = mb_strtolower(($product->sku ?? '') . ' ' . ($product->name ?? '') . ' ' . ($product->description ?? ''));

        return match (true) {
            str_contains($text, 'mesh') => Product::CATEGORY_MESH_WIFI,
            str_contains($text, 'access point') || str_contains($text, ' ap-') || str_contains($text, 'hla-ap') => Product::CATEGORY_ACCESS_POINT,
            str_contains($text, 'switch') || str_contains($text, 'hla-sw') => Product::CATEGORY_SWITCH,
            str_contains($text, 'usb') => Product::CATEGORY_USB_WIFI,
            str_contains($text, 'camera') || str_contains($text, 'cam') => Product::CATEGORY_CAMERA,
            str_contains($text, 'range') || str_contains($text, 'mo rong') => Product::CATEGORY_EXTENDER,
            default => Product::CATEGORY_ROUTER,
        };
    }
};
