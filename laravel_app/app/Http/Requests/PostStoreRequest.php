<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostStoreRequest extends FormRequest
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
            'content' => 'nullable|string|max:1000',
            'files' => 'sometimes|array',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg,gif,mp4,mov,webm|max:51200',
        ];
    }
    
    public function messages(): array
    {
        return [
            'content.nullable' => '投稿内容は必須です。',
            'content.max' => '投稿内容は1000文字以内で入力してください。',
            'files.*.mimes' => 'ファイル形式はjpg,jpeg,png,webp,svg,gif,mp4,mov,webmのみです。',
            'files.*.max' => 'ファイルサイズは50MB以内で入力してください。',
            'files.*.uploaded' => 'ファイルのアップロードに失敗しました。',
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
