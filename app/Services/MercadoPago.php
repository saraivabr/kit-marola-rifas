<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Rifa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MercadoPago
{
    private const API_URL = 'https://api.mercadopago.com/v1/payments';

    private string $accessToken = '';

    public function __construct()
    {
        $this->accessToken = env('MERCADOPAGO_ACCESS_TOKEN');
    }

    public function generatePix(Order $order, Rifa $rifa)
    {
        $notificationUrl = route('payment.update');

        $qty = $order->quantity ?: (is_array($order->numbers_reserved) && count($order->numbers_reserved) ? count($order->numbers_reserved) : 1);

        if (empty($this->accessToken)) {
            $amount = floatval(number_format($qty * $rifa->price, 2, '.', ''));
            $fakeId = rand(1000000000, 9999999999);
            $fakePixCode = "00020126580014br.gov.bcb.pix0136kitmarola-pix@saraiva.ai520400005303986540".number_format($amount, 2, '', '')."5802BR5916KIT MAROLA RIFAS6009SAO PAULO62070503***6304E8F2";

            return (object) [
                'id' => $fakeId,
                'ticket_url' => 'https://kitmarola.saraiva.ai',
                'payment_method_id' => 'pix',
                'date_of_expiration' => Carbon::parse($order->expire_at)->toIso8601String(),
                'transaction_amount' => $amount,
                'qr_code' => $fakePixCode,
            ];
        }

        /**
         * Não funciona caso o endereço seja localhost
         */
        if (env('APP_DEBUG') === true) {
            $notificationUrl = str_replace('localhost', 'laravel.test', $notificationUrl);
        }

        $customerNameParts = collect(explode(' ', $order->customer_fullname));

        $response = Http::acceptJson()
            ->withToken($this->accessToken)
            ->contentType('application/json')
            ->post(self::API_URL, [
                'transaction_amount' => floatval(number_format($qty * $rifa->price, 2, '.', '')),
                'description' => "Rifa \"{$rifa->title}\"",
                'payment_method_id' => 'pix',
                'notification_url' => $notificationUrl,
                'date_of_expiration' => Carbon::parse($order->expire_at)->format('Y-m-d\TH:i:s.vP'),
                'payer' => [
                    'email' => $order->customer_email,
                    'first_name' => $customerNameParts->first(),
                    'last_name' => $customerNameParts->last(),
                ],
            ]);

        if ($response->failed()) {
            return $response->throw();
        }

        return (object) [
            'id' => $response->json('id'),
            'ticket_url' => $response->json('point_of_interaction.transaction_data.ticket_url'),
            'payment_method_id' => $response->json('payment_method_id'),
            'date_of_expiration' => $response->json('date_of_expiration'),
            'transaction_amount' => $response->json('transaction_amount'),
            'qr_code' => $response->json('point_of_interaction.transaction_data.qr_code'),
        ];
    }

    /**
     * Busca as informações de um pedido através da API
     */
    public function getPayment(int $paymentId): object
    {
        if (empty($this->accessToken)) {
            return (object) [
                'status' => 'approved',
                'date_approved' => now()->toIso8601String(),
            ];
        }

        $response = Http::withToken($this->accessToken)
            ->get(self::API_URL."/{$paymentId}");

        if ($response->failed()) {
            return $response->throw();
        }

        return $response->object();
    }
}
