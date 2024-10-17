<?php

namespace App\Http\Requests\Todo;

use Illuminate\Foundation\Http\FormRequest;

class QueryRequest extends FormRequest
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
        return [];
    }

    public function getFilterParams(): array
    {
        $filterString = $this->getFilter();
        $params = [];

        if ($filterString) {
            $filters = explode(' ', $filterString);

            foreach ($filters as $filter) {
                if (strpos($filter, '=') !== false) {
                    list($key, $value) = explode('=', $filter, 2);
                    if (!empty($key) && !empty($value)) {
                        $params[$key] = $value;
                    }
                }
            }
        }

        return $params;
    }
    public function getFilter(): ?string
    {
        return $this->input('filter');
    }

    public function getName(): ?string
    {
        $params = $this->getFilterParams();
        return $params['name'] ?? null;
    }
    public function getDescription(): ?string
    {
        $params = $this->getFilterParams();
        return $params['description'] ?? null;
    }

    public function getCategory(): ?string
    {
        $params = $this->getFilterParams();
        return $params['category'] ?? null;
    }

    public function getShared(): ?bool
    {
        $params = $this->getFilterParams();
        return isset($params['shared']) ? filter_var($params['shared'], FILTER_VALIDATE_BOOLEAN) : null;
    }
}
