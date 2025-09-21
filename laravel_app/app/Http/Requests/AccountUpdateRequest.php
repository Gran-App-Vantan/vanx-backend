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
    public function rules(): array
    {
        return [
            'name' => 'string|min:1|max:32',
            'user_path' => 'string|max:8|unique:users,user_path|regex:/^[a-zA-Z0-9@_-]+$/',
            'user_icon' => 'image|mimes:png,jpeg,jpg,gif,svg,webp|max:5120',
        ];

    }
    public function messages(): array
    {
        return [
            'name.min' => '名前は1文字以上で入力してください',
            'name.max' => '名前は32文字以下で入力してください',
            'user_path.max' => 'ユーザーIDは8文字以下で入力してください',
            'user_path.unique' => 'ユーザーIDは既に使用されています',
            'user_path.regex' => 'ユーザーIDは半角英数字と@,-,_のみ使用できます',
            'user_icon.image' => 'アイコンは画像ファイルで入力してください',
            'user_icon.mimes' => 'アイコンはpng、jpeg、jpg、gif、svg、webp形式で入力してください',
            'user_icon.max' => 'アイコンは5MB以下で入力してください',
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
