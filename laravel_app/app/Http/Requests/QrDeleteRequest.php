<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class QrDeleteRequest extends FormRequest
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
            'id' => 'required|exists:point_recovery_sessions,id',
        ];
    }
    
    public function messages(): array
    {
        return [
            'id.required' => 'QRコードIDが入力されていません',
            'id.exists' => 'QRコードIDが見つかりませんでした',
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
