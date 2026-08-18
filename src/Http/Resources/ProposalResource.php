<?php

namespace Whilesmart\Proposals\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'customer_id' => $this->customer_id,
            'estimate_id' => $this->estimate_id,
            'number' => $this->number,
            'title' => $this->title,
            'status' => $this->status?->value,
            'sections' => $this->sections ?? [],
            'sent_at' => $this->sent_at?->toDateString(),
            'accepted_at' => $this->accepted_at?->toDateString(),
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
