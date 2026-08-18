<?php

namespace Whilesmart\Proposals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class UpdateProposalRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerOfBoundModel('proposal');
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:200'],
            'customer_id' => ['nullable', 'integer'],
            'estimate_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:40'],
            'sections' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
