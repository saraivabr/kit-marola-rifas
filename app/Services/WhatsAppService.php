<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $serverUrl;
    private string $token;
    private string $owner;

    public function __construct()
    {
        $this->serverUrl = rtrim(config('whatsapp.server_url', 'https://escreveai.uazapi.com'), '/');
        $this->token = config('whatsapp.token', '3e9532b9-11d7-4b85-a90d-8e2c0221a9e2');
        $this->owner = config('whatsapp.owner', '5511980948484');
    }

    /**
     * Sanitiza o telefone do cliente para o formato internacional aceito pelo WhatsApp
     */
    public function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Se tiver 10 ou 11 dígitos (DDD + número), adiciona o DDI 55 do Brasil
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            $digits = '55' . $digits;
        }

        return $digits;
    }

    /**
     * Envia mensagem de confirmação de pagamento com os números da sorte
     */
    public function sendPaymentConfirmation(Order $order, array $allocatedNumbers = []): bool
    {
        try {
            $phone = $this->formatPhone($order->customer_telephone);
            if (empty($phone)) {
                Log::warning("WhatsAppService: Telefone inválido para o pedido #{$order->id}");
                return false;
            }

            $numbers = !empty($allocatedNumbers) ? $allocatedNumbers : ($order->numbers_reserved ?? []);
            if (is_array($numbers)) {
                $numbersString = implode(', ', $numbers);
            } else {
                $numbersString = (string) $numbers;
            }

            $rifaTitle = $order->rifa ? $order->rifa->title : 'Rifa Kit Marola';
            $qty = $order->quantity ?: (is_array($numbers) ? count($numbers) : 1);
            $instagram = $order->customer_instagram ?: 'Não informado';

            $message = "🌿 *KIT MAROLA TABACARIA RIFAS* 🌿\n\n";
            $message .= "Olá, *{$order->customer_fullname}*! 🎉\n";
            $message .= "Seu pagamento via Pix foi *CONFIRMADO* com sucesso!\n\n";
            $message .= "📦 *Rifa:* {$rifaTitle}\n";
            $message .= "🎟️ *Cotas Adquiridas:* {$qty}\n\n";
            $message .= "🔥 *SEUS NÚMEROS DA SORTE:*\n";
            $message .= "👉 *{$numbersString}* 👈\n\n";
            $message .= "📱 *Instagram Cadastrado:* {$instagram}\n\n";
            $message .= "🍀 Desejamos muita sorte! O sorteio será anunciado e transmitido pelo nosso perfil.\n";
            $message .= "Acompanhe o andamento da rifa pelo link:\n";
            $message .= "👉 https://kitmarola.saraiva.ai";

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$this->serverUrl}/send/text", [
                'number' => $phone,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsAppService: Confirmação enviada para {$phone} (Pedido #{$order->id})");
                return true;
            }

            Log::error("WhatsAppService: Falha ao enviar para {$phone} - " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error("WhatsAppService Exception: " . $e->getMessage());
            return false;
        }
    }
}
