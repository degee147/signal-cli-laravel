<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receiver' => $this->receiver,
            'sender' => $this->sender,
            'sender_name' => $this->sender_name,
            'body' => $this->body,
            'replied' => $this->replied == "0" ? false : true,
            'receive_timestamp' => $this->receive_timestamp,
            'delivered_timestamp' => $this->delivered_timestamp,
            'message_timestamp' => $this->message_timestamp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
