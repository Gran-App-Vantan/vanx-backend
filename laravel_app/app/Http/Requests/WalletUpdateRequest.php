<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class WalletUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'point' => 'required|numeric',
            'service_name' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|string|in:get,use,import,export,expire',
        ];
    }
    public function messages(): array
    {
        return [
            'point.required' => 'ポイントは必須です',
            'point.numeric' => 'ポイントは数値で入力してください',
            'service_name.required' => 'サービス名は必須です',
            'description.required' => '説明は必須です',
            'type.required' => 'タイプは必須です',
            'type.in' => 'タイプはget,use,import,export,expireのいずれかで入力してください',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'messages' => collect($validator->errors()->messages())
                    ->flatten()
                    ->toArray()
            ], 422)
        );
    }
}
