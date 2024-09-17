<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $isRead = boolval($this->read_at);
        $message = $this->data['message'] ?? null;
        $data = $this->data;
        if (isset($data['message'])) {
            unset($data['message']);
        }       
        return [
            'id' => $this->id,
            'type' => class_basename($this->type),
            'data' => $this->when(count($data), $data),
            'message' => $message,
            'lue' => $isRead,
            'date_lue' => $this->when($isRead && $this->read_at, function () {
                return $this->read_at->format('Y-m-d H:i:s');
            }),
            'dure' => $this->created_at->diffForHumans(), 
            'date' => $this->created_at->format('Y-m-d H:i:s'), 
        ];
    }
}
