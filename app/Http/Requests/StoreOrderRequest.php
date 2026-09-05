<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Rules\Rifa as RulesRifa;
use App\Rules\RifaQuantity;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara os dados antes da validação caso o cliente já exista pelo telefone
     */
    protected function prepareForValidation(): void
    {
        $phone = $this->input('telephone');
        $cleanPhone = preg_replace('/\D/', '', (string) $phone);

        // Se tem telefone mas não tem outros dados, busca no histórico
        if ($cleanPhone && (empty($this->fullname) || empty($this->email) || empty($this->instagram))) {
            $prevOrder = Order::where(function ($query) use ($cleanPhone, $phone) {
                $query->where('customer_telephone', 'like', "%{$cleanPhone}%")
                    ->orWhere('customer_telephone', $phone);
            })
            ->whereNotNull('customer_fullname')
            ->latest('id')
            ->first();

            if ($prevOrder) {
                if (empty($this->fullname) && $prevOrder->customer_fullname) {
                    $this->merge(['fullname' => $prevOrder->customer_fullname]);
                }
                if (empty($this->email) && $prevOrder->customer_email) {
                    $this->merge(['email' => $prevOrder->customer_email]);
                }
                if (empty($this->instagram) && $prevOrder->customer_instagram) {
                    $this->merge(['instagram' => $prevOrder->customer_instagram]);
                }
            }
        }

        // Garante confirmação do telefone idêntica
        if (empty($this->confirmTelephone) && !empty($this->telephone)) {
            $this->merge(['confirmTelephone' => $this->telephone]);
        }

        // Se ainda não tiver e-mail, gera um institucional seguro
        if (empty($this->email) && !empty($cleanPhone)) {
            $this->merge(['email' => "cliente_{$cleanPhone}@kitmarola.saraiva.ai"]);
        }

        // Se não informou instagram, coloca padrão opcional
        if (empty($this->instagram)) {
            $this->merge(['instagram' => '@cliente']);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|max:64|min:3|regex:/^(?:[A-zÀ-ü]{3,}).*(?:[A-zÀ-ü]{3,})$/i',
            'email' => 'required|email|max:100',
            'telephone' => 'required|min:10|max:20',
            'confirmTelephone' => 'required|same:telephone',
            'instagram' => 'required|string|max:64|min:2',
            'terms' => 'required|accepted',
            'quantity' => ['required', 'integer', new RifaQuantity],
            'rifa' => ['required', new RulesRifa],
        ];
    }
}

