<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PaymentStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isApproved = $this->order->status === Order::STATUS_PAID && (bool) $this->date_approved;

        return [
            'is_approved' => $isApproved,
            'date_approved' => $this->when((bool) $this->date_approved, Carbon::parse($this->date_approved)),
            'numbers' => $isApproved ? ($this->order->numbers_reserved ?? []) : [],
        ];
    }
}
