<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    protected function formatDate($date)
    {
        if ($date instanceof Carbon) {
            return $date->format('d-m-Y H:i');
        }
        return Carbon::parse($date)->format('d-m-Y H:i');
    }
}
