<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ReactionsGetRequest extends FormRequest
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
            // 'category' => 'required|string|in:all,emoji,nature,food,activity,travel,symbol,original',//本番
            'category' => 'required|string|in:all,face,nature,food,activity,travel,object,symbol,original',//TEST
        ];
    }
    public function messages(): array
    {
        return [
            'category.required' => 'カテゴリーが入力されていません',
            'category.in' => 'カテゴリーが正しくありません',
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
