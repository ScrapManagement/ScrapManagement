<?php

namespace App\Http\Controllers\DashBoard\Order;

use Illuminate\Http\Request;
use App\Models\Product\Order;
use App\Models\Product\Product;
use App\Models\Product\OrderItem;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('buyer')->latest()->get();
        return view('DashBoard.Order.view', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('DashBoard.Order.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $order = Order::create([
            'buyer_id'    => auth()->id() ?? 1,
            'total_price' => 0,
            'status'      => 'pending',
        ]);

        $total = 0;
        foreach ($request->products as $item) {

            $product = Product::findOrFail($item['id']);

            $subtotal = $product->price * $item['quantity'];

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'seller_id'  => $product->user_id,
                'price'      => $product->price,
                'quantity'   => $item['quantity'],
                'subtotal'   => $subtotal,
            ]);
            $total += $subtotal;
        }

        $order->update([
            'total_price' => $total,
        ]);

        return redirect()->route('order.index')->with('success', 'Order created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['buyer', 'items.product'])->findOrFail($id);
        return view('DashBoard.Order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        return view('DashBoard.Order.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status,
        ]);
        return redirect()->route('order.index')->with('success', 'Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $order->items()->delete();
        $order->delete();
        return redirect()->route('order.index')->with('success', 'Order deleted successfully');
    }
}
