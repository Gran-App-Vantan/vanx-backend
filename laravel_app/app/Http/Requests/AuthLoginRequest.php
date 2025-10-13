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
            'user.name' => 'required|string|min:1|max:32|exists:users,name',
            'user.password' => 'required|string|min:8|max:16',
        ];
    }
    public function messages(): array
    {
        return [
            'user.name.required' => '名前は必須です',
            'user.name.max' => '名前は32文字以下で入力してください',
            'user.name.exists' => '指定された名前は存在しません',
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
