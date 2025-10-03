<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeliveryState;

class WebhookController extends Controller
{
    protected string $webhookSecret;

    public function __construct()
    {
        $this->webhookSecret = config('services.biteship_api.key');
    }

    public function biteship(Request $request)
    {
        if (empty($request->all())) {
            return response()->json(['ok' => true], 200);
        }

        $authHeader = $request->header('Authorization');

        if ($authHeader !== $this->webhookSecret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();

        $order = Order::where('biteship_order_id', $data['order_id'])->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $deliveryState = DeliveryState::firstOrCreate(
            ['biteship_status' => $data['status']],
            [
                'name'                => ucfirst(str_replace('_', ' ', $data['status'])),
                'biteship_status_name' => ucfirst(str_replace('_', ' ', $data['status'])),
                'is_biteship_status'  => true,
                'description'         => $data['status'],
            ]
        );

        switch ($data['event']) {
            case 'order.status':
                $order->update([
                    'delivery_state_id' => $deliveryState->id,
                    'biteship_waybill'  => $data['courier_waybill_id'] ?? $order->biteship_waybill,
                ]);
                break;

            case 'order.price':
                if ($order->shipping_method_id) {
                    ShippingMethod::where('id', $order->shipping_method_id)->update([
                        'price' => $data['price'],
                    ]);
                }

                $order->update([
                    'delivery_state_id' => $deliveryState->id,
                ]);
                break;

            case 'order.waybill_id':
                $order->update([
                    'biteship_waybill'  => $data['courier_waybill_id'],
                    'delivery_state_id' => $deliveryState->id,
                ]);
                break;
        }

        return response()->json(['success' => true]);
    }
}
