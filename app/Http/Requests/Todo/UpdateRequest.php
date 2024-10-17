<?php

namespace App\Http\Requests\Todo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'isShared' => 'required|boolean',
            'isPublic' => 'required|boolean',
            'sharedLink' => 'nullable|string|required_if:isShared,true',
            'sharedEmails' => 'nullable|array',
            'sharedEmails.*' => 'email',
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('sharedEmails', 'required|array', function ($input) {
            return $input->isShared && !$input->isPublic;
        });
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getCategory(): array
    {
        return $this->category;
    }
    public function isShared(): bool
    {
        return $this->isShared;
    }
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function getSharedEmails(): ?array
    {
        return $this->sharedEmails;
    }
    public function getSharedLink(): ?string
    {
        return $this->sharedLink;
    }
}
