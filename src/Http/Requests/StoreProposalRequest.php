<?php

namespace Whilesmart\Proposals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class StoreProposalRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerInRequest();
    }

    public function rules(): array
    {
        return [
            'owner_type' => ['required', 'string'],
            'owner_id' => ['required'],
            'customer_id' => ['nullable', 'integer'],
            'estimate_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:200'],
            'status' => ['nullable', 'string', 'max:40'],
            'sections' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
