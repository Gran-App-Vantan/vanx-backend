<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AccountUpdateRequest extends FormRequest
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
    protected function prepareForValidation()
    {
        // 空文字列をnullに変換
        $this->merge([
            'name' => $this->name === '' ? null : $this->name,
            'password' => $this->password === '' ? null : $this->password,
        ]);
    }

    public function rules(): array
    {
        // デバッグ用にリクエストデータをログに出力
        \Log::info('Account update request data:', $this->all());
        \Log::info('Files in request:', $this->allFiles());
        \Log::info('User ID: ' . auth()->id());
        
        return [
            'name' => 'nullable|string|max:32|unique:users,name,' . auth()->id(),
            'password' => 'nullable|string|min:8|max:32',
            'user_icon' => 'nullable|file|max:5120',
        ];

    }
    public function messages(): array
    {
        return [
            'name.min' => 'ゆうせいを愛してください',
            'name.max' => '名前は32文字以下で入力してください',
            'name.unique' => '名前は既に使用されています',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.max' => 'パスワードは32文字以下で入力してください',
            'user_icon.image' => 'アイコンは画像ファイルで入力してください',
            'user_icon.mimes' => 'アイコンはpng、jpeg、jpg、gif、svg、webp形式で入力してください',
            'user_icon.max' => 'アイコンは5MB以下で入力してください',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        \Log::error('Validation failed for account update:', [
            'errors' => $validator->errors()->toArray(),
            'request_data' => $this->all(),
            'user_id' => auth()->id()
        ]);
        
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'プロフィールの更新に失敗しました。',
                'errors' => collect($validator->errors()->messages())
                    ->flatten()
                    ->toArray()
            ], 422)
        );
    }
}
