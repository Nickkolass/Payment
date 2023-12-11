<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'payout_token' => $this->resource['payout_token'],
            'first6' => $this->resource['first6'],
            'last4' => $this->resource['last4'],
            'card_type' => $this->resource['card_type'],
            'issuer_country' => $this->resource['issuer_country'],
        ];
    }
}
