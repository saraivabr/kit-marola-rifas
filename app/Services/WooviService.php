<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Rifa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooviService
{
    private const BASE_URL = 'https://api.openpix.com.br/api/v1';

    private string $appId;
    private ?string $defaultPartnerPixKey;
    private string $defaultPartnerName;
    private int $defaultPartnerSplitPercent;

    public function __construct()
    {
        $this->appId = env('WOOVI_APP_ID', 'Q2xpZW50X0lkXzAzYjg5ZDQ1LTQyOGYtNDU2Yy1hNGUxLTQ3YWJhMzZkM2M0YzpDbGllbnRfU2VjcmV0XzZ5ZTkwV1VHcnNqVDhzWXR2SFN2Q0lRNWhvUE15a09QWVYrV0xxRTJPN2s9');
        $this->defaultPartnerPixKey = env('WOOVI_PARTNER_PIX_KEY');
        $this->defaultPartnerName = env('WOOVI_PARTNER_NAME', 'Parceiro Kit Marola');
        $this->defaultPartnerSplitPercent = (int) env('WOOVI_PARTNER_SPLIT_PERCENT', 70);
    }

    /**
     * Gera uma cobrança Pix na Woovi/OpenPix com Split de 70% para o parceiro
     */
    public function generatePix(Order $order, Rifa $rifa): object
    {
        $qty = $order->quantity ?: (is_array($order->numbers_reserved) && count($order->numbers_reserved) ? count($order->numbers_reserved) : 1);
        $totalCents = (int) round($qty * $rifa->price * 100);
        $amount = floatval(number_format($totalCents / 100, 2, '.', ''));
        $correlationID = "km_order_{$order->id}_" . time();

        $partnerPixKey = $rifa->partner_pix_key ?: $this->defaultPartnerPixKey;
        $partnerName = $rifa->partner_name ?: $this->defaultPartnerName;
        $partnerPercent = (int) ($rifa->partner_split_percent ?: $this->defaultPartnerSplitPercent);

        $payload = [
            'correlationID' => $correlationID,
            'value' => $totalCents,
            'comment' => "Kit Marola #{$order->id} - {$rifa->title}",
        ];

        if (!empty($order->customer_fullname)) {
            $payload['customer'] = [
                'name' => $order->customer_fullname,
                'phone' => $order->customer_telephone ?: null,
                'email' => $order->customer_email ?: null,
            ];
        }

        // Configuração de Split (70% parceiro / 30% plataforma Fellipe Saraiva)
        $hasSplit = false;
        if (!empty($partnerPixKey)) {
            $normalizedPixKey = trim($partnerPixKey);
            // Se for telefone brasileiro (ex: 11988801548), garante prefixo internacional +55
            $digitsOnly = preg_replace('/\D/', '', $normalizedPixKey);
            if (strlen($digitsOnly) === 11 && !str_starts_with($normalizedPixKey, '+')) {
                $normalizedPixKey = '+55' . $digitsOnly;
            }

            $this->ensureSubaccount($partnerName, $normalizedPixKey);

            $partnerCents = (int) round(($partnerPercent / 100) * $totalCents);
            if ($partnerCents > 0 && $partnerCents < $totalCents) {
                $payload['splits'] = [
                    [
                        'pixKey' => $normalizedPixKey,
                        'value' => $partnerCents,
                        'splitType' => 'SPLIT_SUB_ACCOUNT',
                    ]
                ];
                $hasSplit = true;
            }
        }

        $response = Http::acceptJson()
            ->withHeaders(['Authorization' => $this->appId])
            ->contentType('application/json')
            ->post(self::BASE_URL . '/charge', $payload);

        // Se falhou e tinha split, tenta fallback seguro sem split para não barrar a compra
        if ($response->failed() && $hasSplit) {
            Log::warning('Woovi: Falha ao criar cobrança com split. Tentando cobrança direta.', [
                'error' => $response->body(),
                'payload' => $payload,
            ]);

            unset($payload['splits']);
            $response = Http::acceptJson()
                ->withHeaders(['Authorization' => $this->appId])
                ->contentType('application/json')
                ->post(self::BASE_URL . '/charge', $payload);
        }

        if ($response->failed()) {
            Log::error('Woovi: Erro crítico ao gerar Pix na API OpenPix', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Erro ao comunicar com o gateway Woovi: ' . $response->body());
        }

        $data = $response->json();
        $charge = $data['charge'] ?? $data;

        $id = $charge['correlationID'] ?? $charge['identifier'] ?? $correlationID;
        $ticketUrl = $charge['paymentLinkUrl'] ?? null;
        $brCode = $charge['brCode'] ?? ($charge['paymentMethods']['pix']['brCode'] ?? '');
        $qrCodeImg = $charge['qrCodeImage'] ?? ($charge['paymentMethods']['pix']['qrCodeImage'] ?? '');
        $expiresAt = $charge['expiresDate'] ?? Carbon::parse($order->expire_at)->toIso8601String();

        return (object) [
            'id' => $id,
            'ticket_url' => $ticketUrl,
            'payment_method_id' => 'pix',
            'date_of_expiration' => $expiresAt,
            'transaction_amount' => $amount,
            'qr_code' => $brCode,
            'qr_code_img' => $qrCodeImg,
        ];
    }

    /**
     * Consulta status de pagamento na Woovi
     */
    public function getPayment(string $paymentId): object
    {
        $response = Http::acceptJson()
            ->withHeaders(['Authorization' => $this->appId])
            ->get(self::BASE_URL . "/charge/{$paymentId}");

        if ($response->failed()) {
            Log::warning("Woovi: Consulta falhou para id {$paymentId}", ['body' => $response->body()]);
            return (object) [
                'status' => 'pending',
                'date_approved' => null,
            ];
        }

        $charge = $response->json('charge') ?? $response->json();
        $rawStatus = strtoupper($charge['status'] ?? '');

        $isApproved = in_array($rawStatus, ['COMPLETED', 'CONFIRMED', 'PAID', 'SUCCESS']);

        return (object) [
            'status' => $isApproved ? 'approved' : 'pending',
            'date_approved' => $isApproved ? ($charge['updatedAt'] ?? now()->toIso8601String()) : null,
            'raw_status' => $rawStatus,
        ];
    }

    /**
     * Garante que uma subconta com a chave Pix esteja registrada na OpenPix
     */
    public function ensureSubaccount(string $name, string $pixKey): bool
    {
        try {
            $response = Http::acceptJson()
                ->withHeaders(['Authorization' => $this->appId])
                ->contentType('application/json')
                ->post(self::BASE_URL . '/subaccount', [
                    'name' => $name,
                    'pixKey' => $pixKey,
                ]);

            if ($response->successful()) {
                Log::info("Woovi: Subconta criada com sucesso para {$name} ({$pixKey})");
                return true;
            }

            // Se já existe, tudo bem
            $body = $response->body();
            if (str_contains($body, 'já') || str_contains($body, 'already')) {
                return true;
            }

            Log::warning("Woovi: Subconta não pôde ser criada automaticamente: {$body}");
            return false;
        } catch (\Throwable $e) {
            Log::error("Woovi: Erro ao garantir subconta: " . $e->getMessage());
            return false;
        }
    }
}
