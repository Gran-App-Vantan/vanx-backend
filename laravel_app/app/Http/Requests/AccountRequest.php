<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'auth.name' => 'required|string|min:1|max:32',
            'auth.user_path' => 'required|string|min:8|max:8|unique:users,user_path',
            'auth.password' => 'required|string|min:8|max:16',
            'auth.checked_password' => 'required|string|same:auth.password',
        ];
    }

    public function messages(): array
    {
        return [
            'auth.name.required' => '名前は必須です',
            'auth.name.min' => '名前は1文字以上で入力してください',
            'auth.name.max' => '名前は32文字以下で入力してください',
            'auth.user_path.required' => 'ユーザー名は必須です',
            'auth.user_path.min' => 'ユーザー名は8文字以上で入力してください',
            'auth.user_path.max' => 'ユーザー名は8文字以下で入力してください',
            'auth.user_path.unique' => 'ユーザーIDは既に使用されています',
            'auth.password.required' => 'パスワードは必須です',
            'auth.password.min' => 'パスワードは8文字以上で入力してください',
            'auth.password.max' => 'パスワードは16文字以下で入力してください',
            'auth.checked_password.required' => '確認用のパスワードが必須です',
            'auth.checked_password.same' => 'パスワードが一致しません',
        ];
    }
}
