<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\CoinTransaction;

class WalletController extends Controller
{
    public function balance()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'coins' => $user->coins
        ]);
    }


    public function transactions()
    {
        $user = auth()->user() ;

        $transactions = CoinTransaction::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
