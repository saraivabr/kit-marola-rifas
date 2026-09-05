<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Busca dados do cliente pelo número de telefone (WhatsApp)
     */
    public function lookup(Request $request): JsonResponse
    {
        $rawPhone = $request->query('telephone', '');
        $cleanPhone = preg_replace('/\D/', '', $rawPhone);

        if (strlen($cleanPhone) < 10) {
            return response()->json(['found' => false]);
        }

        // Busca o pedido mais recente associado a este telefone
        $order = Order::where(function ($query) use ($cleanPhone, $rawPhone) {
            $query->where('customer_telephone', 'like', "%{$cleanPhone}%")
                ->orWhere('customer_telephone', $rawPhone)
                ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(customer_telephone, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') LIKE ?", ["%{$cleanPhone}%"]);
        })
        ->whereNotNull('customer_fullname')
        ->where('customer_fullname', '!=', '')
        ->latest('id')
        ->first();

        if ($order) {
            return response()->json([
                'found' => true,
                'customer' => [
                    'fullname' => $order->customer_fullname,
                    'email' => $order->customer_email,
                    'telephone' => $order->customer_telephone,
                    'instagram' => $order->customer_instagram,
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }
}
