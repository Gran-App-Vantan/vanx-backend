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
            'user.user_path' => 'required|string|min:1|max:8|exists:users,user_path',
            'user.password' => 'required|string|min:8|max:16',
        ];
    }
    public function messages(): array
    {
        return [
            'user.user_path.required' => 'パスは必須です',
            'user.user_path.exists' => '指定されたパスは存在しません',
            'user.user_path.max' => 'パスは8文字以下で入力してください',
            'user.password.required' => 'パスワードが必須です',
            'user.password.min' => 'パスワードは8文字以上で入力してください',
            'user.password.max' => 'パスワードは16文字以下で入力してください',
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
