<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_path' => 'required|string|min:8|max:8|exists:users,user_path',
            'password' => 'required|string|min:8|max:16',
        ];
    }
    public function messages(): array
    {
        return [
            'user_path.required' => 'ユーザーIDが必須です',
            'user_path.exists' => '指定されたユーザーIDは存在しません',
            'user_path.min' => 'ユーザーIDは8文字以上で入力してください',
            'user_path.max' => 'ユーザーIDは8文字以下で入力してください',
            'password.required' => 'パスワードが必須です',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.max' => 'パスワードは16文字以下で入力してください',
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
