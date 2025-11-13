<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class QrCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->user_job === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer',
            'service_name' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|string',
            'expires_at' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => '金額が入力されていません',
            'amount.string' => '金額は文字列で入力してください',
            'amount.integer' => '金額は整数で入力してください',
            'service_name.required' => 'サービス名が入力されていません',
            'service_name.string' => 'サービス名は文字列で入力してください',
            'description.required' => '説明が入力されていません',
            'description.string' => '説明は文字列で入力してください',
            'type.required' => 'タイプが入力されていません',
            'type.string' => 'タイプは文字列で入力してください',
            'expires_at.integer' => '有効期限は整数で入力してください。また、有効期限は今から何分後かを入力してください',
        ];
    }   

    public function failedAuthorization()
    {
        return response()->json([
            'success' => false,
            'message' => '管理者権限がありません',
        ], 401);
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
