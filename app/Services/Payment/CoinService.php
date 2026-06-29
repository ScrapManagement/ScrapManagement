<?php

namespace App\Services\Payment;

use App\Models\Payment\CoinTransaction;
use App\Models\Payment\Package;
use App\Models\Payment\ProductUnlock;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;


class CoinService
{
    public function purchasePackage($user, Package $package)
    {
        return DB::transaction(function () use ($user, $package) {

            $user->increment('coins', $package->coins);

            CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $package->coins,
                'reference_type' => 'package',
                'reference_id' => $package->id
            ]);

            return true;
        });
    }

    public function calculateUnlockCost(Product $product): int
    {
        $baseCost = 2;

        $priceFactor = floor($product->price / 1000);

        $quantityFactor = floor($product->quantity / 100);

        $subTotal = $baseCost + $priceFactor + $quantityFactor;

        $totalCost = $subTotal * ($product->category->material_priority ?? 1);

        return (int) $totalCost;
    }

    public function unlockProduct($user, Product $product)
    {
        return DB::transaction(function () use ($user, $product) {

            if (ProductUnlock::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists()
            ) {

                return 'already_unlocked';
            }

            $cost = $this->calculateUnlockCost($product);

            if ($user->coins < $cost) {
                return 'insufficient_coins';
            }

            $user->decrement('coins', $cost);

            CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $cost,
                'reference_type' => 'product',
                'reference_id' => $product->id
            ]);

            ProductUnlock::create([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);

            return 'success';
        });
    }
}
