<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $r->validate(['status' => 'nullable|in:pending,confirmed,completed,cancelled', 'q' => 'nullable|string|max:100']);
        $orders = $r->attributes->get('shop')->orders()->withCount('items')->when($r->status, fn ($q, $s) => $q->where('status', $s))->when($r->q, fn ($q, $s) => $q->where(fn ($q) => $q->where('buyer_name', 'like', '%'.$s.'%')->orWhere('number', ltrim($s, '#0'))))->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Request $r, string $id)
    {
        $order = $r->attributes->get('shop')->orders()->with(['items', 'events'])->where('public_id', $id)->firstOrFail();
        $notification = DB::table('outbox')->where('shop_id', $order->shop_id)->where('order_id', $order->id)->first();

        return view('orders.show', compact('order', 'notification'));
    }

    public function status(Request $r, string $id, OrderService $service)
    {
        $r->validate(['status' => 'required|in:confirmed,completed,cancelled']);
        $service->transition($r->attributes->get('shop'), $id, $r->status, $r->user()->id);

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
