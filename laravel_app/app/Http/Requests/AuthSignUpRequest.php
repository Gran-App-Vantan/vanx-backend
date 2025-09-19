<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthSignUpRequest extends FormRequest
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
            'user.name' => 'required|string|min:1|max:32',
            'user.user_path' => 'required|string|min:8|max:8|unique:users,user_path',
            'user.password' => 'required|string|min:8|max:16',
            'user.checked_password' => 'required|string|same:user.password',
        ];
    }

    public function messages(): array
    {
        return [
            'user.name.required' => '名前は必須です',
            'user.name.min' => '名前は1文字以上で入力してください',
            'user.name.max' => '名前は32文字以下で入力してください',
            'user.user_path.required' => 'ユーザーIDは必須です',
            'user.user_path.min' => 'ユーザーIDは8文字以上で入力してください',
            'user.user_path.max' => 'ユーザーIDは8文字以下で入力してください',
            'user.user_path.unique' => 'ユーザーIDは既に使用されています',
            'user.password.required' => 'パスワードは必須です',
            'user.password.min' => 'パスワードは8文字以上で入力してください',
            'user.password.max' => 'パスワードは16文字以下で入力してください',
            'user.checked_password.required' => '確認用のパスワードが必須です',
            'user.checked_password.same' => 'パスワードが一致しません',
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

