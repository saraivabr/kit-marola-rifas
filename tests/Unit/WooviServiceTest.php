<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Rifa;
use App\Services\WooviService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WooviServiceTest extends TestCase
{
    public function test_woovi_generates_charge_with_split_70_30(): void
    {
        Http::fake([
            'https://api.openpix.com.br/api/v1/subaccount' => Http::response(['success' => true], 200),
            'https://api.openpix.com.br/api/v1/charge' => Http::response([
                'charge' => [
                    'correlationID' => 'km_order_1_123456',
                    'identifier' => 'ident_123',
                    'value' => 299,
                    'status' => 'ACTIVE',
                    'brCode' => '000201...TEST_PIX_BRCODE...',
                    'qrCodeImage' => 'https://api.woovi.com/test.png',
                    'paymentLinkUrl' => 'https://woovi.com/pay/test',
                    'expiresDate' => '2026-09-06T20:00:00Z',
                ]
            ], 200),
        ]);

        $rifa = new Rifa();
        $rifa->id = 1;
        $rifa->title = 'Boné RAW + Case';
        $rifa->price = 2.99;
        $rifa->partner_pix_key = 'parceiro@exemplo.com';
        $rifa->partner_name = 'Parceiro Raw';
        $rifa->partner_split_percent = 70;

        $order = new Order();
        $order->id = 1;
        $order->quantity = 1;
        $order->customer_fullname = 'João Silva';
        $order->customer_telephone = '5511999999999';
        $order->customer_email = 'joao@silva.com';

        $service = new WooviService();
        $response = $service->generatePix($order, $rifa);

        $this->assertEquals('km_order_1_123456', $response->id);
        $this->assertEquals('000201...TEST_PIX_BRCODE...', $response->qr_code);
        $this->assertEquals(2.99, $response->transaction_amount);

        Http::assertSent(function ($request) {
            if ($request->url() === 'https://api.openpix.com.br/api/v1/charge') {
                $data = $request->data();
                // 70% de 299 centavos é 209 centavos
                return $data['value'] === 299
                    && isset($data['splits'])
                    && $data['splits'][0]['pixKey'] === 'parceiro@exemplo.com'
                    && $data['splits'][0]['value'] === 209;
            }
            return true;
        });
    }

    public function test_woovi_handles_payment_check(): void
    {
        Http::fake([
            'https://api.openpix.com.br/api/v1/charge/charge_123' => Http::response([
                'charge' => [
                    'status' => 'COMPLETED',
                    'updatedAt' => '2026-09-05T20:30:00Z',
                ]
            ], 200),
        ]);

        $service = new WooviService();
        $status = $service->getPayment('charge_123');

        $this->assertEquals('approved', $status->status);
        $this->assertEquals('2026-09-05T20:30:00Z', $status->date_approved);
    }
}
