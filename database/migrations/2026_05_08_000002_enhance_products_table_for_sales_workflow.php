<?php

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
            $table->string('sku')->nullable()->after('id');
            $table->unsignedInteger('stock')->default(0)->after('price');
            $table->boolean('is_active')->default(true)->after('stock');
        });

        DB::table('products')
            ->orderBy('id')
            ->get()
            ->each(function (object $product): void {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'sku' => 'HLA-' . str_pad((string) $product->id, 4, '0', STR_PAD_LEFT),
                        'stock' => 20,
                        'is_active' => true,
                    ]);
            });

        Schema::table('products', function (Blueprint $table) {
            $table->unique('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'stock', 'is_active']);
        });
    }
};
